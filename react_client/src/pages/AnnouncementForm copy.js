import React, {useEffect, useRef, useState, useMemo} from "react"
import { useParams } from 'react-router-dom'
import request from "../helpers/request"
import i18n from "../i18n"
import JoditEditor from "jodit-react"
import CheckTree from "../components/single/check_module_v2/CheckTree"
import Announcement from "../components/single/Announcement"
import Swal from 'sweetalert2'
import history from "../helpers/history"
import Checkbox from "../components/single/Checkbox"
import Flatpickr from 'react-flatpickr'
import "flatpickr/dist/flatpickr.css"
import moment from 'moment'
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import { faFolderPlus, faClose } from "@fortawesome/free-solid-svg-icons"
import jsonToFormDataHelper from "../helpers/json_to_form_data"

const AnnouncementForm = (props) => {

    const {announcementId} = useParams()
    const editorEl = useRef(null)
    const editorEn = useRef(null)
    const fileRef = useRef(null)
    const [tags, setTags] = useState([])
    const [mostUsedTags, setMostUsedTags] = useState([])
    const [isEdit, setIsEdit] = useState(false)
    const [files, setFiles] = useState([])
    const [placeholder, setPlaceholder] = useState('test')
    const [announcement, setAnnouncement] = useState({
        body:               "",
        eng_body:           "",
        title:              "",
        eng_title:          "",
        event_end_time:     "",
        event_location:     null,
        event_start_time:   null,
        gmaps:              null,
        has_eng:            null,
        is_event:           null,
        is_pinned:          null,
        pinned_until:       null,
        tags:               []
    })
    const config = {
			readonly: false, // all options from https://xdsoft.net/jodit/doc/,
			placeholder: 'Πληκτρολογήστε το κείμενό σας',
            cleanHTML: {
                allowTags: 'p,a[href],table,tr,td, img[src=1.png]', // allow only <p>,<a>,<table>,<tr>,<td>,<img> tags and,
                cleanOnPaste: false
            }
	}

    useEffect(() => {
        request.get('all_tags').then(response => {
            //console.log(response)
            setTags(transformTags(response.data))
        })
        request.get('most_used_tags').then(response => {
            //console.log(response)
            setMostUsedTags(response.data)
        })
        if (announcementId) {
            request.get(`announcements/edit_view/${announcementId}`).then(response => {
                if (response.data.data) {
                    const a = response.data.data
                    a.tags = a.tags.map(t => {
                        const title = t.title ? t.title : t.name
                        return {
                            label: title,
                            value: t.id
                        }
                    })
                    a.attachments_old = a.attachments
                    a.attachments = []
                    setAnnouncement(a)
                    setIsEdit(true)
                }
            })
        }

        
    }, [])

    const transformTags = (tags) => {
        return tags.map(t => {
            if (t.children && t.children.length > 0) {
                return {
                    label: t.title,
                    value: t.id,
                    children: transformTags(t.children)
                }
            } else {
                return {
                    label: t.title,
                    value: t.id
                }
            }
            
        })
    }

    const setCustomDate = (key, date) => {
        const d = `${moment(date).format("YYYY-MM-DDT00:00:00.000")}Z`
        setAnnouncement({
            ...announcement,
            [key]: d
        })
    }

    const changeAnnouncement = (key, value) => {
        setAnnouncement({
            ...announcement,
            [key]: value
        })
    }

    const checkChange = (checked, tag) => {
        if (checked) {
            if (announcement.tags.filter(t => t.value === tag.id).length === 0) {
                setAnnouncement({
                    ...announcement,
                    tags: [
                        ...announcement.tags,
                        {
                            label: tag.title,
                            value: tag.id
                        }
                    ]
                })
            }
        } 
        else {
            if (announcement.tags.filter(t => t.value === tag.id).length > 0) {
                setAnnouncement({
                    ...announcement,
                    tags: announcement.tags.filter(t => t.value !== tag.id)
                })
            }
        }
    }

    const validForm = () => {
        // --todo--
        return true
    }

    const saveAnnouncement = () => {
        if (validForm()) {
            if (isEdit) {
                const formData = jsonToFormDataHelper.jsonToFormData({
                    ...announcement,
                    tags: announcement.tags.map(t => t.value),
                    attachments_old: null,
                    _method: 'PUT'
                })

                files.forEach(f => {
                    formData.append('attachments[]', f)
                })
                formData.append(
                    "attachments_old",
                    JSON.stringify(announcement.attachments_old)
                );
                // announcement.attachments_old.forEach(f => {
                //     formData.append('attachments_old[]', f.id)
                // })

                request.post(`announcements/${announcement.id}`, formData, false, true).then(response => {
                    //console.log(response)
                    Swal.fire({
                        title: 'Επιτυχία!',
                        text: "Η ανακοίνωση επεξεργάστηκε επιτυχώς!",
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: i18n.t('Εντάξει!')
                    })
                })
            }
            else {
                const formData = jsonToFormDataHelper.jsonToFormData({
                    ...announcement,
                    tags: announcement.tags.map(t => t.value)
                })

                files.forEach(f => {
                    formData.append('attachments[]', f)
                })
                
                request.post(`announcements`, formData, false, true).then(response => {
                    //console.log(response)

                    if (response.status === 200 || response.status === 201) {
                        Swal.fire({
                            title: 'Επιτυχία!',
                            text: "Η ανακοίνωση αναρτήθηκε επιτυχώς!",
                            icon: 'success',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: i18n.t('Εντάξει!')
                        }).then(() => {
                            history.push('/my_announcements')
                        })
                    }
                    else {
                        Swal.fire({
                            title: 'Αποτυχία!',
                            text: "Κάτι πήγε στραβά!",
                            icon: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: i18n.t('Εντάξει!')
                        })
                    }
                    
                })
            }
            
        }
    }

    const changeAnnouncementTags = (value) => {
        //console.log(value)
        mostUsedTags.forEach(t => {
            const tag = document.getElementById(`checkbox_tag_${t.id}`)
            if (tag) 
                tag.checked = false
        })
        value.forEach(t => {
            const tag = document.getElementById(`checkbox_tag_${t.value}`)
            if (tag) 
                tag.checked = true
        })
        setAnnouncement({
            ...announcement,
            tags: value
        })
    }

    const uploadFiles = (e) => {
        const fileArray = Array.from(e.target.files)
        //console.log(e)
        setFiles(fileArray.concat(files))
    }

    const removeOldAttachment = (file) => {
        setAnnouncement({
            ...announcement,
            attachments_old: announcement.attachments_old.filter(f => f.id !== file.id)
        })
    }

    const removeFile = (index) => {
        // setAnnouncement({
        //     ...announcement,
        //     attachments_old: announcement.attachments_old.filter(f => f.id !== file.id)
        // })
    }

    return (
        <div className="container">
            <div className="main-header">
                <h1>{isEdit ? i18n.t('Επεξεργασία ανακοίνωσης') : i18n.t('Προσθήκη ανακοίνωσης')}</h1>
            </div>
            <div className="row">
                <div className="col-md-6">
                    <div className="form-group">
                        <div className={`form-control form-control-xl`}>
                            <label htmlFor="title">{i18n.t('Τίτλος')}</label>
                            <input type="text" id="title" value={announcement.title} onChange={(e) => changeAnnouncement('title', e.target.value)} placeholder={i18n.t('Προσθέστε έναν τίτλο ανακοίνωσης')} />
                        </div>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="form-group">
                        <div className={`form-control form-control-xl`}>
                            <label htmlFor="eng_title">{i18n.t('Τίτλος στα αγγλικά')}</label>
                            <input type="text" id="eng_title" value={announcement.eng_title} onChange={(e) => changeAnnouncement('eng_title', e.target.value)} placeholder={i18n.t('Προσθέστε έναν τίτλο ανακοίνωσης')} />
                        </div>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className={`form-control form-control-xl`}>
                        <label htmlFor="body">{i18n.t('Κείμενο')}</label>
                        <JoditEditor
                            // config={config}
                            className="jodit-editor"
                            ref={editorEl}
                            value={announcement.body}
                            tabIndex={1} // tabIndex of textarea
                            onBlur={newContent => changeAnnouncement('body', newContent)} // preferred to use only this option to update the content for performance reasons
                            onChange={newContent => {}}
                        />
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className={`form-control form-control-xl`}>
                        <label htmlFor="eng_body">{i18n.t('Κείμενο στα αγγλικά')}</label>
                        <JoditEditor
                            // config={config}
                            className="jodit-editor"
                            ref={editorEn}
                            value={announcement.eng_body}
                            tabIndex={1} // tabIndex of textarea
                            onBlur={newContent => changeAnnouncement('eng_body', newContent)} // preferred to use only this option to update the content for performance reasons
                            onChange={newContent => {}}
                        />
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className="form-control form-control-xl">
                        <label>Ετικέτες</label>
                        {
                            (mostUsedTags && mostUsedTags.length > 0) &&
                            <>
                                <span>Ετικέτες που χρησιμοποιείτε συχνά</span>
                                {
                                    mostUsedTags.map(t => {
                                        const checked = announcement.tags && announcement.tags.filter(tag => tag.value === t.id).length > 0
                                        return (
                                            <Checkbox
                                                key={`most_used_tag_${t.id}`}
                                                id={`checkbox_tag_${t.id}`}
                                                title={t.title}
                                                checked={checked}
                                                onChange={(e) => checkChange(e, t)}
                                            />
                                        )
                                    })
                                }
                            </> 
                        }
                        {
                            tags &&
                            <CheckTree 
                                className="react-select"
                                prefix="react"
                                id="search-tags"
                                options={tags}
                                isMulti
                                placeholder={i18n.t('Επιλέξτε ετικέτες')}
                                value={announcement.tags}
                                onChange={(e) => changeAnnouncementTags(e)}
                                checkAllByParent={true}
                            />
                        }
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-6">
                    <div className="form-control">
                        <Checkbox
                            title={i18n.t('Επισήμανση ως σημαντική')}
                            onChange={(e) => changeAnnouncement('is_pinned', e)}
                        />
                    </div>
                </div>
                <div className="col-md-6">
                    <div className={`form-control ${announcement.is_pinned ? '' : 'hidden'}`}>
                        <label htmlFor="pinned-date">{i18n.t('Εμφάνιση μέχρι')}</label>
                        <Flatpickr 
                            className='form-date' 
                            value={announcement.pinned_until} 
                            onChange={date => setCustomDate('updatedAfter', date[0])} 
                            id='pinned-date' 
                            options={{
                                dateFormat: 'd-m-Y'
                            }}
                        />
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className="form-control">
                        <button className="btn btn-primary" onClick={() => fileRef.current.click()}><FontAwesomeIcon icon={faFolderPlus} /> {i18n.t('Ανέβασμα αρχείων')}</button>
                        <input type="file" className="hidden" onChange={(e) => uploadFiles(e)} ref={fileRef} multiple/>
                        {
                            (announcement && announcement.attachments_old && announcement.attachments_old.length > 0) &&
                            announcement.attachments_old.map(f => {
                                return (
                                    <div className="file">
                                        <span className="file-name">{f.filename}</span>
                                        <span className="file-remove" onClick={() => removeOldAttachment(f)}><FontAwesomeIcon icon={faClose} /></span>
                                    </div>
                                )
                            })
                        }
                        {
                            (files && files.length > 0) &&
                            files.map(f => {
                                return (
                                    <div className="file">
                                        <span className="file-name">{f.name}</span>
                                        <span className="file-remove" onClick={() => removeFile(f)}><FontAwesomeIcon icon={faClose} /></span>
                                    </div>
                                )
                            })
                        }
                    </div>
                </div>
            </div>
            <div className="row">
                <button className="btn btn-success" onClick={saveAnnouncement}>{i18n.t('Αποθήκευση')}</button>
            </div>
        </div>
    )
}

export default AnnouncementForm