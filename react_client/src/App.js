import logo from './logo.svg'
import Header from './components/layout/Header'
import Home from './pages/Home'
import { Switch, Route, Router, Redirect  } from 'react-router-dom'
import history from './helpers/history'
import React from 'react'

import { useEffect, useState, useCallback } from 'react'
import config from './config'
import LoginSuccess from './pages/LoginSuccess'
import Announcements from './pages/Announcements'
import { useCookies } from 'react-cookie'
import storage from './helpers/storage'
import request from './helpers/request'
import cookieHelper from './helpers/cookie'
import MyAnnouncements from './pages/MyAnnouncements'
import AnnouncementForm from './pages/AnnouncementForm'
import AnnouncementFormNew from './pages/AnnouncementFormNew'
import About from './pages/About'
import 'bootstrap/dist/css/bootstrap.min.css'
import './App.css'
import './assets/scss/main.css'
import User from './pages/User'
import FullAnnouncement from './components/single/FullAnnouncement'
import Test from './pages/Test'
import i18n from './i18n'
import Utils from './helpers/utils'

function App() {
  
  const [user, setUser] = useState(null)
  const [, updateState] = useState();
  const forceUpdate = useCallback(() => updateState({}), []);

  const [darkThemeEnabled, setDarkThemeEnabled] = useState(
    localStorage.getItem('dark-theme') === true || localStorage.getItem('dark-theme') === 'true' 
  )
  useEffect(() => {

    i18n.onLanguageChange(() => {
      forceUpdate()
    })

    setThemeColors(darkThemeEnabled)
    const t = cookieHelper.get('token')
    if (t) {
      storage.set('token', t)
    }

    checkLoginStatus()
  }, [])

  const checkLoginStatus = function () { 
    const c = cookieHelper.get('token')
    //console.log(c)
    if (c && c !== '') {
      storage.set('token', c)
      request.get('auth/whoami').then(response => {
        if (response.data && response.status === 200) {
          if (user === null) {
            forceUpdate()
          }
          storage.set('me', response.data)
          setUser(response.data)
        }
        else {
          setUser(null)
        }
      })
    }
  }
  
  const redirectToApiSSO = async () => {
    Utils.redirectToApiSSO()
    
  }
  const reloadPage = () => {
    window.location.reload()
  }
  const logout = () => {
    

    request.post(`logout`).then(response => {
      storage.set('token', null)
      storage.set('me', null)
      setUser(null)
      cookieHelper.delete('token')
      forceUpdate()
    })
  }

  const setThemeColors = (dark) => {
    /**
     navbar-dark bg-dark
     */
    const nav = document.querySelector('.navbar')
    if (dark) {
      nav.classList.add('navbar-dark')
      nav.classList.add('bg-dark')
      document.body.classList.remove('light-theme')
      document.body.classList.add('dark-theme')
    } else {
      document.body.classList.remove('dark-theme')
      document.body.classList.add('light-theme')

      nav.classList.remove('navbar-dark')
      nav.classList.remove('bg-dark')
    }

    setDarkThemeEnabled(dark)
    localStorage.setItem('dark-theme', dark)
  }
  
  const PrivateRoute = ({ children, ...rest }) => {
    return (
      <Route
        {...(cookieHelper.get('token')) ? rest : {}}
        render={({ location }) => {
          return (cookieHelper.get('token')) ? (
            children
          ) : (
            <Redirect
              to={{
                pathname: "/announcements",
                state: { from: location },
              }}
            />
          )
        }}
      />
    )
  }

  return (
    <div className="App">
      <div className='content'>
      <Router 
        basename={config.basename}
        history={history}
      >
        <Header
        loginProp={redirectToApiSSO}
        logoutProp={logout}
        setThemeColors={setThemeColors}
        darkThemeEnabled={darkThemeEnabled}
        user={user}
        />
        <Switch>
          <Route path="/login_success">
            <LoginSuccess checkStatus={checkLoginStatus} />
          </Route>
          <Route path="/about">
            <About />
          </Route>
          <Route exact path="/announcements/:announcementId">
            <FullAnnouncement />
          </Route>
          <Route path="/announcements">
            <Announcements />
          </Route>
          
          <PrivateRoute path="/my_announcements">
            <MyAnnouncements />
          </PrivateRoute>
          <PrivateRoute path="/add_announcement">
            <AnnouncementFormNew />
          </PrivateRoute>
          <PrivateRoute path="/account">
            <User />
          </PrivateRoute>
          <PrivateRoute path="/edit_announcement/:announcementId">
            <AnnouncementFormNew />
          </PrivateRoute>

          <Route path="/">
            <Announcements />
          </Route>
        </Switch>
      </Router>
      </div>
    </div>
  )
}

export default App
