import React, {useEffect, useRef, useState} from "react"
import useDidMountEffect from "../helpers/useDidMountEffect"
import { useParams } from 'react-router-dom'
import request from "../helpers/request"
import i18n from "../i18n"
import CheckTree from "../components/single/check_module_v2/CheckTree"
import Swal from 'sweetalert2'
import history from "../helpers/history"
import Checkbox from "../components/single/Checkbox"
import Flatpickr from 'react-flatpickr'
import "flatpickr/dist/flatpickr.css"
import moment from 'moment'
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome"
import { faFolderPlus, faClose } from "@fortawesome/free-solid-svg-icons"
import jsonToFormDataHelper from "../helpers/json_to_form_data"
import {EditorState} from 'draft-js';
import { Editor } from 'react-draft-wysiwyg';
import 'react-draft-wysiwyg/dist/react-draft-wysiwyg.css';
import 'draft-js/dist/Draft.css';
import { convertToHTML } from 'draft-convert';
import { AtomicBlockUtils } from "draft-js"
import { ContentState, convertFromHTML } from "draft-js"
import createImagePlugin from '@draft-js-plugins/image';

const AnnouncementForm = (props) => {

    var timeoutEl = null
    var timeoutEn = null
    const imagePlugin = createImagePlugin();
    const {announcementId} = useParams()
    const [editorStateEl, setEditorStateEl] = useState(EditorState.createEmpty())
    const [editorStateEn, setEditorStateEn] = useState(EditorState.createEmpty())
    const fileRef = useRef(null)
    const [tags, setTags] = useState([])
    const [mostUsedTags, setMostUsedTags] = useState([])
    const [isEdit, setIsEdit] = useState(false)
    const [files, setFiles] = useState([])
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

    const toolbar = {
        options: ['inline', 'blockType', 'list', 'textAlign', 'link', 'image', 'remove', 'history'],
        blockType: {
            inDropdown: true,
            options: ['Normal', 'H1', 'H2', 'H3', 'H4', 'H5', 'H6'],
            className: undefined,
            component: undefined,
            dropdownClassName: undefined,
        },
        image: {
            className: undefined,
            component: undefined,
            popupClassName: undefined,
            urlEnabled: false,
            uploadEnabled: true,
            alignmentEnabled: true,
            uploadCallback: undefined,
            previewImage: false,
            inputAccept: 'image/gif,image/jpeg,image/jpg,image/png,image/svg',
            alt: { present: false, mandatory: false },
            defaultSize: {
              height: 'auto',
              width: 'auto',
            },
        },
    }

    useEffect(() => {
        request.get('all_tags').then(response => {
            setTags(transformTags(response.data))
        })
        request.get('most_used_tags').then(response => {
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
                    a.pinned_until = a.pinned_until ? new Date(a.pinned_until) : ''
                    a.event_start_time = a.event_start_time ? new Date(a.event_start_time) : ''
                    a.event_end_time = a.event_end_time ? new Date(a.event_end_time) : ''
                    setAnnouncement(a)
                    setIsEdit(true)
                    const blocksFromHTML = convertFromHTML(a.body)
                    //console.log("blocks", blocksFromHTML)
                    setEditorStateEl(EditorState.createWithContent(customContentStateConverter(
                        ContentState.createFromBlockArray(
                            blocksFromHTML.contentBlocks,
                            blocksFromHTML.entityMap
                        )
                    )))

                    const blocksFromHTMLEn = convertFromHTML(a.eng_body)
                    //console.log("blocks", blocksFromHTML)
                    setEditorStateEn(EditorState.createWithContent(customContentStateConverter(
                        ContentState.createFromBlockArray(
                            blocksFromHTMLEn.contentBlocks,
                            blocksFromHTMLEn.entityMap
                        )
                    )))
                    
                }
            })
        }

        
    }, [])

    const customContentStateConverter = (contentState) => {
        // changes block type of images to 'atomic'
        const newBlockMap = contentState.getBlockMap().map((block) => {
            const entityKey = block.getEntityAt(0);
            if (entityKey !== null) {
                const entityBlock = contentState.getEntity(entityKey);
                const entityType = entityBlock.getType();
                switch (entityType) {
                    case 'IMAGE': {
                        const newBlock = block.merge({
                            type: 'atomic',
                            text: 'img',
                        });
                        return newBlock;
                    }
                    default:
                        return block;
                }
            }
            return block;
        });
        const newContentState = contentState.set('blockMap', newBlockMap);
        return newContentState;
    }

    useEffect(() => {
        // let html = convertToHTML(editorStateEl.getCurrentContent());
        if (timeoutEl) window.clearTimeout(timeoutEl)
        timeoutEl = setTimeout(() => {
            const html = convertToHTML({
                entityToHTML: (entity, originalText) => {
                  if (entity.type === 'IMAGE') {          
                    return `<img src="${entity.data.src}" />`;
                  }
                  return originalText;
                },
            })(editorStateEl.getCurrentContent());
            // //console.log(editorStateEl.getCurrentContent())
            changeAnnouncement('body', html);
        }, 1000)
    }, [editorStateEl]);

    useEffect(() => {
        // let html = convertToHTML(editorStateEl.getCurrentContent());
        if (timeoutEn) window.clearTimeout(timeoutEn)
        timeoutEn = setTimeout(() => {
            const html = convertToHTML({
                entityToHTML: (entity, originalText) => {
                  if (entity.type === 'IMAGE') {          
                    return `<img src="${entity.data.src}" />`;
                  }
                  return originalText;
                },
            })(editorStateEn.getCurrentContent());
            // //console.log(editorStateEn.getCurrentContent())
            changeAnnouncement('body', html);
        }, 1000)
    }, [editorStateEn]);

    // useEffect(() => {
    //     let html = convertToHTML(editorStateEn.getCurrentContent());
    //     changeAnnouncement('eng_body', html);
    // }, [editorStateEn]);

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
        console.log(date)
        // const d = `${moment(date).format("YYYY-MM-DDThh:mm:00.000")}Z`
        
        const d = `${moment(date).format("YYYY-MM-DDThh:mm:00.000")}`
        setAnnouncement({
            ...announcement,
            [key]: date
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
                    pinned_until: announcement.pinned_until ? moment(announcement.pinned_until).format("YYYY-MM-DD 00:00") : null,
                    event_start_time: announcement.event_start_time ? moment(announcement.event_start_time).format("YYYY-MM-DD HH:ss") : null,
                    event_end_time: announcement.event_end_time ? moment(announcement.event_end_time).format("YYYY-MM-DD HH:ss") : null,
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
                    pinned_until: announcement.pinned_until ? moment(announcement.pinned_until).format("YYYY-MM-DD 00:00") : null,
                    event_start_time: announcement.event_start_time ? moment(announcement.event_start_time).format("YYYY-MM-DD 00:00") : null,
                    event_end_time: announcement.event_end_time ? moment(announcement.event_end_time).format("YYYY-MM-DD 00:00") : null,
                    tags: announcement.tags.map(t => t.value)
                })

                files.forEach(f => {
                    formData.append('attachments[]', f)
                })
                
                request.post(`announcements`, formData, false, true).then(response => {

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
        setFiles(fileArray.concat(files))
    }

    const removeOldAttachment = (file) => {
        setAnnouncement({
            ...announcement,
            attachments_old: announcement.attachments_old.filter(f => f.id !== file.id)
        })
    }

    const removeFile = (index) => {
        setFiles(files.filter((f, i) => i !== index))
        // setAnnouncement({
        //     ...announcement,
        //     attachments_old: announcement.attachments_old.filter(f => f.id !== file.id)
        // })
    }

    const handlePastedFiles = ( files, editorState, stateFn) => {
        

        const FR = new FileReader();
    
        FR.addEventListener("load", (evt) => {
            stateFn(insertImage(evt.target.result, editorState));
        }); 
            
        FR.readAsDataURL(files[0]);
    }

    const isValidUrl = urlString => {
        var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
      '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
      '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
      '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
      '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
      '(\\#[-a-z\\d_]*)?$','i') // validate fragment locator
    return !!urlPattern.test(urlString)
  }

    const convertUrlToBase64 = ( url, editorState, stateFn ) => {
        if (isValidUrl(url)) {
            isImgUrl(url).then(isImg => {
                if (isImg) {
                    setBase64Image(url, setNewImage, editorState, stateFn)
                }
            })
        }
        
    }

    const setNewImage = (base64, editorState, stateFn) => {
        stateFn(insertImage(base64, editorState));
    }

    const isImgUrl = (url) => {
        const img = new Image();
        img.src = url;
        return new Promise((resolve) => {
          img.onerror = () => resolve(false);
          img.onload = () => resolve(true);
        });
      }

    function setBase64Image(url, callback, editorState, stateFn) {
        var xhr = new XMLHttpRequest();
        xhr.onload = function() {
            var reader = new FileReader();
            reader.onloadend = function() {
                callback(reader.result, editorState, stateFn);
            }
            reader.readAsDataURL(xhr.response);
        };
        xhr.open('GET', url);
        xhr.responseType = 'blob';
        xhr.send();
    }

    const insertImage = (url, editorState) => {
        const contentState = editorState.getCurrentContent();
        const contentStateWithEntity = contentState.createEntity(
            'IMAGE',
            'IMMUTABLE',
            { src: url },)
        const entityKey = contentStateWithEntity.getLastCreatedEntityKey();
        const newEditorState = EditorState.set( editorState, { currentContent: contentStateWithEntity });
        // setEditorStateEl(newEditorState)
        return AtomicBlockUtils.insertAtomicBlock(newEditorState, entityKey, ' ');
    };

    const onChangeEditor = () => {

    }

    const getAnnouncementValue = (key) => {
        if (announcement[key] && typeof announcement[key] !== 'undefined') {
            return announcement[key]
        }

        return ''
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
                            <input type="text" id="title" value={getAnnouncementValue('title')} onChange={(e) => changeAnnouncement('title', e.target.value)} placeholder={i18n.t('Προσθέστε έναν τίτλο ανακοίνωσης')} />
                        </div>
                    </div>
                </div>
                <div className="col-md-6">
                    <div className="form-group">
                        <div className={`form-control form-control-xl`}>
                            <label htmlFor="eng_title">{i18n.t('Τίτλος στα αγγλικά')}</label>
                            <input type="text" id="eng_title" value={getAnnouncementValue('eng_title')} onChange={(e) => changeAnnouncement('eng_title', e.target.value)} placeholder={i18n.t('Προσθέστε έναν τίτλο ανακοίνωσης')} />
                        </div>
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className={`form-control form-control-xl`}>
                        <label htmlFor="body">{i18n.t('Κείμενο')}</label>
                        <Editor 
                            toolbar={toolbar}
                            editorState={editorStateEl} 
                            onEditorStateChange={setEditorStateEl} 
                            wrapperClassName="rich-editor demo-wrapper"
                            editorClassName="demo-editor"
                            handlePastedText={(e) => convertUrlToBase64(e, editorStateEl, setEditorStateEl)}
                            handlePastedFiles={files => handlePastedFiles(files, editorStateEl, setEditorStateEl)}  
                        />
                        
                        
                    </div>
                </div>
            </div>
            <div className="row">
                <div className="col-md-12">
                    <div className={`form-control form-control-xl`}>
                        <label htmlFor="eng_body">{i18n.t('Κείμενο στα αγγλικά')}</label>
                        <Editor 
                            toolbar={toolbar}
                            editorState={editorStateEn} 
                            onEditorStateChange={setEditorStateEn} 
                            wrapperClassName="rich-editor demo-wrapper"
                            editorClassName="demo-editor"
                            handlePastedText={(e) => convertUrlToBase64(e, editorStateEn, setEditorStateEn)}
                            handlePastedFiles={files => handlePastedFiles(files, editorStateEn, setEditorStateEn)}  
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
                                value={getAnnouncementValue('tags')}
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
                            checked={announcement.is_pinned}
                        />
                    </div>
                </div>
                <div className="col-md-6">
                    <div className={`form-control ${announcement.is_pinned ? '' : 'hidden'}`}>
                        <label htmlFor="pinned-date">{i18n.t('Εμφάνιση μέχρι')}</label>
                        <Flatpickr 
                            className='form-date' 
                            value={getAnnouncementValue('pinned_until')} 
                            onChange={date => setCustomDate('pinned_until', date[0])} 
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
                        <Checkbox
                            title={i18n.t('Προσθήκη εκδήλωσης')}
                            onChange={(e) => changeAnnouncement('is_event', e)}
                            checked={announcement.is_event}
                        />
                    </div>
                </div>
                <div className="col-md-12">
                    <div className={`form-control form-control-xl ${announcement.is_event ? '' : 'hidden'}`}>
                        <label htmlFor="event_location">{i18n.t('Τοποθεσία')}</label>
                        <input type="text" id="event_location" value={getAnnouncementValue('event_location')} onChange={(e) => changeAnnouncement('event_location', e.target.value)} placeholder={i18n.t('Εισάγετε την τοποθεσία της εκδήλωσης')} />
                    </div>
                </div>
                <div className="col-md-6">
                    <div className={`form-control ${announcement.is_event ? '' : 'hidden'}`}>
                        <label htmlFor="pinned-date">{i18n.t('Ημ/νία έναρξης')}</label>
                        <Flatpickr 
                            className='form-date' 
                            value={getAnnouncementValue('event_start_time')} 
                            onChange={date => setCustomDate('event_start_time', date[0])} 
                            id='pinned-date' 
                            options={{
                                enableTime: true,
                                dateFormat: 'd-m-Y H:i'
                            }}
                        />
                    </div>
                </div>
                <div className="col-md-6">
                    <div className={`form-control ${announcement.is_event ? '' : 'hidden'}`}>
                        <label htmlFor="pinned-date">{i18n.t('Ημ/νία λήξης')}</label>
                        <Flatpickr 
                            className='form-date' 
                            value={getAnnouncementValue('event_end_time')} 
                            onChange={date => setCustomDate('event_end_time', date[0])} 
                            id='pinned-date' 
                            options={{
                                enableTime: true,
                                dateFormat: 'd-m-Y H:i'
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
                            announcement.attachments_old.map((f, index) => {
                                return (
                                    <div className="file" key={`new-file-${index}`} onClick={() => removeOldAttachment(f)}>
                                        <span className="file-name">{f.filename}</span>
                                        <span className="file-remove"><FontAwesomeIcon icon={faClose} /></span>
                                    </div>
                                )
                            })
                        }
                        {
                            (files && files.length > 0) &&
                            files.map((f, index) => {
                                return (
                                    <div className="file" key={`new-file-${index}`}>
                                        <span className="file-name" onClick={() => removeFile(index)}>{f.name}</span>
                                        <span className="file-remove"><FontAwesomeIcon icon={faClose} /></span>
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