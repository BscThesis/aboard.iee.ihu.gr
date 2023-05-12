import React, { useEffect } from "react";
import i18n from "../i18n";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faGithub } from '@fortawesome/free-brands-svg-icons'

const About = (props) => {

    useEffect(() => {
        const unmountLocaleChange = i18n.onLanguageChange(() => {
            document.title = i18n.t('about_page_title')
        })
        document.title = i18n.t('about_page_title')
        return () => {
            unmountLocaleChange()
        };
    }, [])

    

    return (
        <div className="container mb-4">
            <div className="main-header">
                <h1>{i18n.t('Σχετικά με το project')}</h1>
            </div>
            <div className="row">
                <div className="col-12 fs-18">
                    {i18n.t('Το παρόν σύστημα είναι αποτέλεσμα συνεργασίας πολλών φοιτητών στο πλαίσιο εκπόνησης πτυχιακής ή και διπλωματικής εργασίας')}.&nbsp;
                    {i18n.t('Κατά χρονική σειρά (συν-)εργάστηκαν οι')}:
                </div>
                <div className="col-12 mt-4 fs-18">
                    <ul>
                        <li>
                            Σιδηρόπουλος Αντώνης:&nbsp;{i18n.t('Αρχική ιδέα, σχεδιασμός και επίβλεψη')}.
                        </li>
                        <li>
                            Νικολαΐδης Νικόλας-Χρήστος:&nbsp;{i18n.t('Πτυχιακή εργασία, 2018-2020. Σχεδιασμός και ανάπτυξη της αρχικής έκδοσης της web εφαρμογής (API και web client)')}.
                        </li>
                        <li>
                            Ραφαήλ Μονογιός:&nbsp;{i18n.t('Πτυχιακή εργασία, 2019-2020. Υλοποίηση GUI Client για Android')}.
                        </li>
                        <li>
                            Μπάρμπας Αντώνιος:&nbsp;{i18n.t('Πτυχιακή εργασία, 2020-2021. Σύστημα διάδοσης ειδοποιήσεων')}.
                        </li>
                        <li>
                            Στίνης Γεώργιος:&nbsp;{i18n.t('Πτυχιακή εργασία, 2021-2022. Βελτίωση/Επανασχεδιασμός web client')}.
                        </li>
                        <li>
                            Παπαδόπουλος Παντελεήμων-Νεκτάριος:&nbsp;{i18n.t('Πτυχιακή εργασία, 2021-2022. Βελτίωση/Επανασχεδιασμός WEB API')}.
                        </li>
                        <li>
                            Θεοφάνης Κουστούλας:&nbsp;{i18n.t('Πτυχιακή εργασία, 2022-2023. Βελτίωση/Επανασχεδιασμός web API και web client')}.
                        </li>
                    </ul>
                </div>
                <div className="col-12 mt-4">
                    {i18n.t('Το Τμήμα Μηχανικών Πληροφορικής και Ηλεκτρονικών Συστημάτων ευχαριστεί όλους τους παραπάνω και όλους όσους συνέβαλαν άμεσα ή έμμεσα στην ανάπτυξη αυτού του συστήματος')}.
                </div>
                <div className="col-12 mt-4">
                    {i18n.t('Για περισσότερες πληροφορίες σχετικά με το project επισκεφθείτε την σελίδα του project στο')} &nbsp; 
                    <a className="btn btn-primary" href="https://github.com/BscThesis/aboard.iee.ihu.gr" target="_blank"><FontAwesomeIcon icon={faGithub} /> Github</a>.
                </div>
            </div>
        </div>
    )
}

export default About;