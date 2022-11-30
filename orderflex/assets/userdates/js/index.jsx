import React from 'react';
//import ReactDOM from 'react-dom';
import ReactDOM from "react-dom/client";
import '../css/index.css';

//import App from './App';
import App from './BasicTable';
//import MatchInfo from './MatchInfo';

// ReactDOM.render(
// <React.StrictMode>
// <App />
// </React.StrictMode>,
//     document.getElementById('root')
// );

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);

// const matchinginfo = ReactDOM.createRoot(document.getElementById("matching-info"));
// matchinginfo.render(
//     <React.StrictMode>
//         <MatchInfo />
//     </React.StrictMode>
// );

//https://react-bootstrap.github.io/getting-started/introduction/
//https://www.tutsmake.com/react-17-bootstrap-datepicker-example/
//https://stackoverflow.com/questions/37560863/react-datepicker-bootstrap-up-to-date
