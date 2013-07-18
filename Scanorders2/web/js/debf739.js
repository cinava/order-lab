/* =============================================================
 * bootstrap-combobox.js v1.1.3
 * =============================================================
 * Copyright 2012 Daniel Farrell
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

!function( $ ) {

 "use strict";

  var Combobox = function ( element, options ) {
    this.options = $.extend({}, $.fn.combobox.defaults, options)
    this.$source = $(element)
    this.$container = this.setup()
    this.$element = this.$container.find('input[type=text]')
    this.$target = this.$container.find('input[type=hidden]')
    this.$button = this.$container.find('.dropdown-toggle')
    this.$menu = $(this.options.menu).appendTo('body')
    this.matcher = this.options.matcher || this.matcher
    this.sorter = this.options.sorter || this.sorter
    this.highlighter = this.options.highlighter || this.highlighter
    this.shown = false
    this.selected = false
    this.refresh()
    this.transferAttributes()
    this.listen()
  }

  /* NOTE: COMBOBOX EXTENDS BOOTSTRAP-TYPEAHEAD.js
     ========================================== */

  Combobox.prototype = $.extend({}, $.fn.typeahead.Constructor.prototype, {

    constructor: Combobox

  , setup: function () {
      var combobox = $(this.options.template)
      this.$source.before(combobox)
      this.$source.hide()
      return combobox
    }

  , parse: function () {
      var that = this
        , map = {}
        , source = []
        , selected = false
      this.$source.find('option').each(function() {
        var option = $(this)
        if (option.val() === '') {
          that.options.placeholder = option.text()
          return
        }
        map[option.text()] = option.val()
        source.push(option.text())
        if(option.attr('selected')) selected = option.text()
      })
      this.map = map
      if (selected) {
        this.$element.val(selected)
        this.$container.addClass('combobox-selected')
        this.selected = true
      }
      return source
    }

  , transferAttributes: function() {
    this.options.placeholder = this.$source.attr('data-placeholder') || this.options.placeholder
    this.$element.attr('placeholder', this.options.placeholder)
    this.$target.prop('name', this.$source.prop('name'))
    this.$target.val(this.$source.val())
    this.$source.removeAttr('name')  // Remove from source otherwise form will pass parameter twice.
    this.$element.attr('required', this.$source.attr('required'))
    this.$element.attr('rel', this.$source.attr('rel'))
    this.$element.attr('title', this.$source.attr('title'))
    this.$element.attr('class', this.$source.attr('class'))
    this.$element.attr('tabindex', this.$source.attr('tabindex'))
    this.$source.removeAttr('tabindex')
  }

  , toggle: function () {
    if (this.$container.hasClass('combobox-selected')) {
      this.clearTarget()
      this.triggerChange()
      this.clearElement()
    } else {
      if (this.shown) {
        this.hide()
      } else {
        this.clearElement()
        this.lookup()
      }
    }
  }

  , clearElement: function () {
    this.$element.val('').focus()
  }

  , clearTarget: function () {
    this.$source.val('')
    this.$target.val('')
    this.$container.removeClass('combobox-selected')
    this.selected = false
  }

  , triggerChange: function () {
    this.$source.trigger('change')
  }

  , refresh: function () {
    this.source = this.parse()
    this.options.items = this.source.length
  }

  // modified typeahead function adding container and target handling
  , select: function () {
      var val = this.$menu.find('.active').attr('data-value')
      this.$element.val(this.updater(val)).trigger('change')
      this.$source.val(this.map[val]).trigger('change')
      this.$target.val(this.map[val]).trigger('change')
      this.$container.addClass('combobox-selected')
      this.selected = true
      return this.hide()
    }

  // modified typeahead function removing the blank handling and source function handling
  , lookup: function (event) {
      this.query = this.$element.val()
      return this.process(this.source)
    }

  // modified typeahead function adding button handling and remove mouseleave
  , listen: function () {
      this.$element
        .on('focus',    $.proxy(this.focus, this))
        .on('blur',     $.proxy(this.blur, this))
        .on('keypress', $.proxy(this.keypress, this))
        .on('keyup',    $.proxy(this.keyup, this))

      if (this.eventSupported('keydown')) {
        this.$element.on('keydown', $.proxy(this.keydown, this))
      }

      this.$menu
        .on('click', $.proxy(this.click, this))
        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
        .on('mouseleave', 'li', $.proxy(this.mouseleave, this))

      this.$button
        .on('click', $.proxy(this.toggle, this))
    }

  // modified typeahead function to clear on type and prevent on moving around
  , keyup: function (e) {
      switch(e.keyCode) {
        case 40: // down arrow
        case 39: // right arrow
        case 38: // up arrow
        case 37: // left arrow
        case 36: // home
        case 35: // end
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
          break

        case 9: // tab
        case 13: // enter
          if (!this.shown) return
          this.select()
          break

        case 27: // escape
          if (!this.shown) return
          this.hide()
          break

        default:
          this.clearTarget()
          this.lookup()
      }

      e.stopPropagation()
      e.preventDefault()
  }

  // modified typeahead function to force a match and add a delay on hide
  , blur: function (e) {
      var that = this
      this.focused = false
      var val = this.$element.val()
      if (!this.selected && val !== '' ) {
        this.$element.val('')
        this.$source.val('').trigger('change')
        this.$target.val('').trigger('change')
      }
      if (!this.mousedover && this.shown) setTimeout(function () { that.hide() }, 200)
    }

  // modified typeahead function to not hide
  , mouseleave: function (e) {
      this.mousedover = false
    }
  })

  /* COMBOBOX PLUGIN DEFINITION
   * =========================== */

  $.fn.combobox = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('combobox')
        , options = typeof option == 'object' && option
      if(!data) $this.data('combobox', (data = new Combobox(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.combobox.defaults = {
  template: '<div class="combobox-container"><input type="hidden" /><input type="text" autocomplete="off" /><span class="add-on btn dropdown-toggle" data-dropdown="dropdown"><span class="caret"/><span class="combobox-clear"><i class="icon-remove"/></span></span></div>'
  , menu: '<ul class="typeahead typeahead-long dropdown-menu"></ul>'
  , item: '<li><a href="#"></a></li>'
  }

  $.fn.combobox.Constructor = Combobox

}( window.jQuery );

/* =============================================================
 * bootstrap-typeahead.js v2.3.0
 * http://twitter.github.com/bootstrap/javascript.html#typeahead
 * =============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function($){

  "use strict"; // jshint ;_;


 /* TYPEAHEAD PUBLIC CLASS DEFINITION
  * ================================= */

  var Typeahead = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.typeahead.defaults, options)
    this.matcher = this.options.matcher || this.matcher
    this.sorter = this.options.sorter || this.sorter
    this.highlighter = this.options.highlighter || this.highlighter
    this.updater = this.options.updater || this.updater
    this.source = this.options.source
    this.$menu = $(this.options.menu)
    this.shown = false
    this.listen()
  }

  Typeahead.prototype = {

    constructor: Typeahead

  , select: function () {
      var val = this.$menu.find('.active').attr('data-value')
      this.$element
        .val(this.updater(val))
        .change()
      return this.hide()
    }

  , updater: function (item) {
      return item
    }

  , show: function () {
      var pos = $.extend({}, this.$element.position(), {
        height: this.$element[0].offsetHeight
      })

      this.$menu
        .insertAfter(this.$element)
        .css({
          top: pos.top + pos.height
        , left: pos.left
        })
        .show()

      this.shown = true
      return this
    }

  , hide: function () {
      this.$menu.hide()
      this.shown = false
      return this
    }

  , lookup: function (event) {
      var items

      this.query = this.$element.val()

      if (!this.query || this.query.length < this.options.minLength) {
        return this.shown ? this.hide() : this
      }

      items = $.isFunction(this.source) ? this.source(this.query, $.proxy(this.process, this)) : this.source

      return items ? this.process(items) : this
    }

  , process: function (items) {
      var that = this

      items = $.grep(items, function (item) {
        return that.matcher(item)
      })

      items = this.sorter(items)

      if (!items.length) {
        return this.shown ? this.hide() : this
      }

      return this.render(items.slice(0, this.options.items)).show()
    }

  , matcher: function (item) {
      return ~item.toLowerCase().indexOf(this.query.toLowerCase())
    }

  , sorter: function (items) {
      var beginswith = []
        , caseSensitive = []
        , caseInsensitive = []
        , item

      while (item = items.shift()) {
        if (!item.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item)
        else if (~item.indexOf(this.query)) caseSensitive.push(item)
        else caseInsensitive.push(item)
      }

      return beginswith.concat(caseSensitive, caseInsensitive)
    }

  , highlighter: function (item) {
      var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
      return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>'
      })
    }

  , render: function (items) {
      var that = this

      items = $(items).map(function (i, item) {
        i = $(that.options.item).attr('data-value', item)
        i.find('a').html(that.highlighter(item))
        return i[0]
      })

      items.first().addClass('active')
      this.$menu.html(items)
      return this
    }

  , next: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , next = active.next()

      if (!next.length) {
        next = $(this.$menu.find('li')[0])
      }

      next.addClass('active')
    }

  , prev: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , prev = active.prev()

      if (!prev.length) {
        prev = this.$menu.find('li').last()
      }

      prev.addClass('active')
    }

  , listen: function () {
      this.$element
        .on('focus',    $.proxy(this.focus, this))
        .on('blur',     $.proxy(this.blur, this))
        .on('keypress', $.proxy(this.keypress, this))
        .on('keyup',    $.proxy(this.keyup, this))

      if (this.eventSupported('keydown')) {
        this.$element.on('keydown', $.proxy(this.keydown, this))
      }

      this.$menu
        .on('click', $.proxy(this.click, this))
        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
        .on('mouseleave', 'li', $.proxy(this.mouseleave, this))
    }

  , eventSupported: function(eventName) {
      var isSupported = eventName in this.$element
      if (!isSupported) {
        this.$element.setAttribute(eventName, 'return;')
        isSupported = typeof this.$element[eventName] === 'function'
      }
      return isSupported
    }

  , move: function (e) {
      if (!this.shown) return

      switch(e.keyCode) {
        case 9: // tab
        case 13: // enter
        case 27: // escape
          e.preventDefault()
          break

        case 38: // up arrow
          e.preventDefault()
          this.prev()
          break

        case 40: // down arrow
          e.preventDefault()
          this.next()
          break
      }

      e.stopPropagation()
    }

  , keydown: function (e) {
      this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27])
      this.move(e)
    }

  , keypress: function (e) {
      if (this.suppressKeyPressRepeat) return
      this.move(e)
    }

  , keyup: function (e) {
      switch(e.keyCode) {
        case 40: // down arrow
        case 38: // up arrow
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
          break

        case 9: // tab
        case 13: // enter
          if (!this.shown) return
          this.select()
          break

        case 27: // escape
          if (!this.shown) return
          this.hide()
          break

        default:
          this.lookup()
      }

      e.stopPropagation()
      e.preventDefault()
  }

  , focus: function (e) {
      this.focused = true
    }

  , blur: function (e) {
      this.focused = false
      if (!this.mousedover && this.shown) this.hide()
    }

  , click: function (e) {
      e.stopPropagation()
      e.preventDefault()
      this.select()
      this.$element.focus()
    }

  , mouseenter: function (e) {
      this.mousedover = true
      this.$menu.find('.active').removeClass('active')
      $(e.currentTarget).addClass('active')
    }

  , mouseleave: function (e) {
      this.mousedover = false
      if (!this.focused && this.shown) this.hide()
    }

  }


  /* TYPEAHEAD PLUGIN DEFINITION
   * =========================== */

  var old = $.fn.typeahead

  $.fn.typeahead = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('typeahead')
        , options = typeof option == 'object' && option
      if (!data) $this.data('typeahead', (data = new Typeahead(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.typeahead.defaults = {
    source: []
  , items: 8
  , menu: '<ul class="typeahead dropdown-menu"></ul>'
  , item: '<li><a href="#"></a></li>'
  , minLength: 1
  }

  $.fn.typeahead.Constructor = Typeahead


 /* TYPEAHEAD NO CONFLICT
  * =================== */

  $.fn.typeahead.noConflict = function () {
    $.fn.typeahead = old
    return this
  }


 /* TYPEAHEAD DATA-API
  * ================== */

  $(document).on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
    var $this = $(this)
    if ($this.data('typeahead')) return
    $this.typeahead($this.data())
  })

}(window.jQuery);
