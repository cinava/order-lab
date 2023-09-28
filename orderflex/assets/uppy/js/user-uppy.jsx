//yarn add @uppy/core @uppy/dashboard @uppy/tus @uppy/webcam

//import { Uppy, Dashboard, Tus } from 'https://releases.transloadit.com/uppy/v3.16.0/uppy.min.mjs'
import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import Tus from '@uppy/tus'
//import RemoteSources from '@uppy/remote-sources'
//import ImageEditor from '@uppy/image-editor'
import Webcam from '@uppy/webcam'
import XHRUpload from '@uppy/xhr-upload'


console.log("before new Uppy");

// const uppy = new Uppy({
//     debug: true,
//     autoProceed: false,
// })
// uppy.use(Dashboard, { target: '#files-drag-drop' })
// uppy.use(Tus, { endpoint: 'https://tusd.tusdemo.net/files/' })
// uppy.on('complete', (result) => {
//     console.log('Upload result:', result)
// })

const uppy = new Uppy({
    debug: true,
    autoProceed: false,
})

uppy.use(Webcam)
uppy.use(Dashboard, {
    inline: true,
    //target: 'body',
    target: '#files-drag-drop',
    plugins: ['Webcam'],
})
uppy.use(XHRUpload, {
    endpoint: 'http://localhost:3020/upload.php',
})


console.log("after Uppy");
