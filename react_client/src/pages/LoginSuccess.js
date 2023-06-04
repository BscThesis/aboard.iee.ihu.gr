import React, { useEffect, useState } from 'react';
import request from '../helpers/request';
import storage from '../helpers/storage';
import { useCookies } from 'react-cookie';
import i18n from '../i18n';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSpinner, faXmarkCircle, faCheckCircle } from '@fortawesome/free-solid-svg-icons'
import history from '../helpers/history';
import cookieHelper from '../helpers/cookie';

const LoginSuccess = (props) => {
    const [loggedIn, setLoggedIn] = useState(false)
    const [error, setError] = useState(null)

    useEffect(() => {
        const params = new URLSearchParams(window.location.search)

        if (params.has('token')) {
            cookieHelper.set('token', null)
            request.post('auth/token', {
                token: params.get('token')
            }).then(response => {
                storage.set('token', response.data.access_token)
                cookieHelper.set('token', response.data.access_token)
                setLoggedIn(true)
                props.checkStatus()
                history.push('/')
            }).catch(error => {
                //console.log(error)
                setError(error)
            })
        }
    }, [])
    return (
        <div className="container transparent justify-center">
            {
            error ?
            <div className="message danger-message">
                <FontAwesomeIcon icon={faXmarkCircle} size="xl"/>
                <h1>
                    {i18n.t("Κάτι πήγε στραβά. Προσπαθήστε ξανά")}
                </h1>
            </div> : loggedIn ?
                <div className="message success-message">
                    <FontAwesomeIcon icon={faCheckCircle} size="xl"/>
                    <h1>
                        {i18n.t("Έχετε συνδεθεί επιτυχώς. Αυτό το παράθυρο θα κλείσει")}
                    </h1>
                </div> :
                <div className="message secondary-message">
                    <FontAwesomeIcon icon={faSpinner} className="spinning" size="xl"/>
                    <h1>
                        {i18n.t("Παρακαλώ περιμένετε")}
                    </h1>
                </div> 
            }
            
            
        </div>
    )
}

export default LoginSuccess;