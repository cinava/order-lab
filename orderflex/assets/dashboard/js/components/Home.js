// ./assets/js/components/Home.js

import React, {Component} from 'react';
// import {Route, Switch, Redirect, Link, withRouter} from 'react-router-dom';
import { Routes ,Route, Redirect, Link, withRouter } from 'react-router-dom';
// import { Routes ,Route, Navigate, Link, withRouter } from 'react-router-dom';
import Users from './Users';
import Posts from './Posts';

class Home extends Component {

    constructor() {
        super();
        this.testflag = $('#testflag').val();
    }
    
    render() {
        return (
            <div>
                <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
                    <Link className={"navbar-brand"} to={"order/index_dev.php/dashboards/react"}> Symfony React Project </Link>
                    <div className="collapse navbar-collapse" id="navbarText">
                        <ul className="navbar-nav mr-auto">
                            <li className="nav-item">
                                <Link className={"nav-link"} to={"/posts"}> Posts </Link>
                            </li>
                            <li className="nav-item">
                                <Link className={"nav-link"} to={"/users"}> Users </Link>
                            </li>
                            <li className="nav-item">
                                {this.testflag}
                            </li>
                        </ul>
                    </div>
                </nav>
                <Routes >
                    <Route exact from="/" to="/users" />
                    <Route path="/users" element={<Users/>} />
                    <Route path="/posts" element={<Posts/>} />
                </Routes >

            </div>
        )
    }
}

export default Home;

