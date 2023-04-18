import React from "react"
import { useEffect } from "react"
import { useState } from "react"
import i18n from '../i18n'
import request from "../helpers/request"
import Tab from 'react-bootstrap/Tab'
import Tabs from 'react-bootstrap/Tabs'
import CheckTree from "../components/single/check_module_v2/CheckTree"
import tagsHelper from "../helpers/tags_helper"
import { useRef } from "react"
import useDidMountEffect from "../helpers/useDidMountEffect"
import Swal from "sweetalert2"

const User = (props) => {
    const [tabKey, setTabKey] = useState('subscriptions')
    const [tags, setTags] = useState([])
    const [userTags, setUserTags] = useState([])
    const [topLevelTags, setTopLevelTags] = useState([])
    const [canShowTags, setCanShowTags] = useState(false)
    const didMount = useRef(false)

    useEffect(() => {
        document.title = i18n.t('account_page_title')
        request.get('subscribetags').then(response => {
            //console.log(response)
            setTags(tagsHelper.transformTags(response.data, 'childrensub_recursive'))
            request.get('auth/subscriptions').then(response => {
                const t = tagsHelper.transformTags(response.data)
                
                setUserTags(t)
                // setTopLevelTags(t.map(t1 => parseInt(t1.value)))
            })
        })
        
    }, [])

    useEffect(() => {
        if (!didMount.current) {
            didMount.current = true
            return
        }

        //console.log("running usertags")

        setCanShowTags(true)
    }, [userTags])

    useDidMountEffect(() => {
        
    }, [topLevelTags])

    const saveSubscriptions = () => {
        request.post('auth/subscribe', {
            tags: topLevelTags
        }).then(response => {
            if (response.status === 200) {
                Swal.fire({
                    title: i18n.t('Επιτυχία!'),
                    text: i18n.t('Οι αλλαγές αποθηκεύτηκαν'),
                    icon: 'success',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: i18n.t('Εντάξει!')
                })
            }
            else {
                Swal.fire({
                    title: i18n.t('Αποτυχία!'),
                    text: i18n.t('Κάτι πήγε στραβά!'),
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: i18n.t('Εντάξει!')
                })
            }
        })
    }

    return (
        <div className="container">
            <Tabs
                id="user-nav-tabs"
                activeKey={tabKey}
                onSelect={(k) => setTabKey(k)}
                className="mb-3"
            >
                <Tab eventKey="subscriptions" title={i18n.t('Ετικέτες')} tabClassName='user-nav'>
                    <div className="row">
                        <div className="col-md-12">
                            <h3>{i18n.t('Ακολουθήστε ετικέτες')}</h3>
                            <p>{i18n.t('Ακολουθώντας ετικέτες μπορείτε να ενημερώνεστε για νέες ανακοινώσεις στις ετικέτες που σας ενδιαφέρουν')}</p>
                        </div>
                    </div>
                    <div className="row">
                        <div className="col-md-6">
                            <label>{i18n.t('Επιλέξτε ετικέτες')}</label>
                            <div className="form-control">
                                {
                                    canShowTags &&
                                    <CheckTree 
                                        className="react-select"
                                        prefix="react"
                                        id="search-tags"
                                        options={tags}
                                        isMulti
                                        placeholder={i18n.t('Επιλέξτε tags')}
                                        value={userTags}
                                        parent_value={topLevelTags}
                                        onChangeParent={(e) => setTopLevelTags(e)}
                                        onChange={(e) => setUserTags(e)}
                                        checkAllByParent={true}
                                        showOnlyParent={true}
                                    />
                                }
                                
                            </div>
                        </div>
                        <div className="col-md-6">
                            <label>{i18n.t('Επιλεγμένες ετικέτες')}</label>
                            <div className="chosen-tags">
                            {
                                topLevelTags.map(t => {
                                    //console.log(t)
                                    return (
                                        <div className="chosen-tag">
                                            {tagsHelper.findOption(t, tags).label}
                                        </div>
                                    )
                                })
                            }
                            </div>
                        </div>
                        <div className="col-md-6">
                            <button className="btn btn-primary" onClick={saveSubscriptions}>{i18n.t('Αποθήκευση')}</button>
                        </div>
                    </div>
                </Tab>
                <Tab eventKey="report" title={i18n.t('Αναφορά προβλήματος')} tabClassName='user-nav'>
                    <div className="row">
                        <div className="col-md-12">
                            <h3>{i18n.t('Αναφορά προβλήματος')}</h3>
                            <p>{i18n.t('Περιγράψτε συνοπτικά το πρόβλημα καθώς και τα βήματα που ακολουθήσατε')}</p>
                        </div>
                    </div>
                </Tab>
            </Tabs>
            
        </div>
    )
}

export default User;