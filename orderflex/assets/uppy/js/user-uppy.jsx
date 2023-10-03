//yarn add @uppy/core @uppy/dashboard @uppy/tus @uppy/webcam

//import { Uppy, Dashboard, Tus } from 'https://releases.transloadit.com/uppy/v3.16.0/uppy.min.mjs'
import Uppy from '@uppy/core'
import Dashboard from '@uppy/dashboard'
import Tus from '@uppy/tus'
//import RemoteSources from '@uppy/remote-sources'
//import ImageEditor from '@uppy/image-editor'
import Webcam from '@uppy/webcam'
import XHRUpload from '@uppy/xhr-upload'

import '@uppy/core/dist/style.css'
import '@uppy/dashboard/dist/style.css'
import '@uppy/webcam/dist/style.css'


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

//var endpointUrl = Routing.generate('employees_upload_chunk_file');
//var endpointUrl = Routing.generate('employees_upload_uppy_file');
var endpointUrl = Routing.generate('tus');


const uppy = new Uppy({
    debug: true,
    autoProceed: false,
    onBeforeFileAdded: (file, files) => {
        console.log("File "+file.name);
        if( Object.hasOwn(files, file.id) ) {
            console.log("Duplicate file "+file.name);
            const name = Date.now() + '_' + file.name
                Object.defineProperty(file.data, 'name', {
                writable: true,
                value: name
            });
            return { ...file, name, meta: { ...file.meta, name } }
        } else {
            console.log("New file "+file.name);
        }
        return file
    },
})

//uppy.use(Webcam)
uppy.use(Dashboard, {
    inline: true,
    target: '#files-drag-drop',
    //plugins: ['Webcam'],
    //width: 300,
    height: 300,
})
// uppy.use(XHRUpload, {
//    endpoint: endpointUrl, //'http://localhost:3020/upload.php',
// })
uppy.use(Tus, {
    endpoint: endpointUrl,
    limit:10,
    //resume: true,
    //autoRetry: true,
    retryDelays: [0, 1000, 3000, 5000],
    removeFingerprintOnSuccess: true
});


console.log("after Uppy");
