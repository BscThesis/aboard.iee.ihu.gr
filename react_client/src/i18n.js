import config from "./config";
import cookieHelper from "./helpers/cookie";

class I18n {
  language = cookieHelper.get('locale') || 'el';
  languageChangeFns = [];
  translations = {
    en: {
      announcements_page_title: `Announcements - ${config.site_title_en}`,
      my_announcements_page_title: `My announcements - ${config.site_title_en}`,
      account_page_title: `My account - ${config.site_title_en}`,
      about_page_title: `About - ${config.site_title_en}`,
      'Σχετικά με το project': 'About the project',
      'Το παρόν σύστημα είναι αποτέλεσμα συνεργασίας πολλών φοιτητών στο πλαίσιο εκπόνησης πτυχιακής ή και διπλωματικής εργασίας': 'The present system is the result of the collaboration of many students in the context of preparing a bachelor\'s or diploma thesis',
      'Κατά χρονική σειρά (συν-)εργάστηκαν οι': 'In chronological order, the people that (co-)worked are',
      "Για να αναφέρετε κάποιο πρόβλημα πατήστε": "To report an issue please click",
      'Αρχική ιδέα, σχεδιασμός και επίβλεψη': 'Original concept, design and supervision',
      'Πτυχιακή εργασία, 2018-2020. Σχεδιασμός και ανάπτυξη της αρχικής έκδοσης της web εφαρμογής (API και web client)': 'Thesis, 2018-2020. Design and development of the initial version of the web application (API and web client)',
      'Πτυχιακή εργασία, 2019-2020. Υλοποίηση GUI Client για Android': 'Thesis, 2019-2020. GUI Client Implementation for Android',
      'Πτυχιακή εργασία, 2020-2021. Σύστημα διάδοσης ειδοποιήσεων': 'Thesis, 2020-2021. Notification dissemination system',
      'Πτυχιακή εργασία, 2021-2022. Βελτίωση/Επανασχεδιασμός web client': 'Thesis, 2021-2022. Web client improvement/redesign',
      'Πτυχιακή εργασία, 2021-2022. Βελτίωση/Επανασχεδιασμός WEB API': 'Thesis, 2021-2022. WEB API Improvement/Redesign',
      'Πτυχιακή εργασία, 2022-2023. Βελτίωση/Επανασχεδιασμός web API και web client': 'Thesis, 2022-2023. Improved/Redesigned web API and web client',
      'Το Τμήμα Μηχανικών Πληροφορικής και Ηλεκτρονικών Συστημάτων ευχαριστεί όλους τους παραπάνω και όλους όσους συνέβαλαν άμεσα ή έμμεσα στην ανάπτυξη αυτού του συστήματος': 'The Department of Information Technology and Electronic Systems Engineering thanks all of the above and all those who contributed directly or indirectly to the development of this system',
      'Για περισσότερες πληροφορίες σχετικά με το project επισκεφθείτε την σελίδα του project στο': 'For more information about the project visit the project page at',
      'εδώ': 'here',
      'Πρόκειται να διαγράψετε αυτή την ανακοίνωση': 'You are about to delete this announcement',
      'Επιθυμείτε να συνεχίσετε;': 'Do you wish to continue?',
      'Διαγραφή': "Delete",
      'Ακύρωση': "Cancel",
      'Αποτυχία': 'Failure',
      'Κάτι πήγε στραβά. Δοκιμάστε αργότερα': 'Something went wrong. Please try again later',
      'Ταξινόμηση': 'Sort by',
      'Επιλέξτε ταξινόμηση': 'Choose sort',
      'Ανά σελίδα': 'Per page',
      'Σημαντικές': 'Important first',
      'Νεότερες': 'Date descending',
      'Παλαιότερες': 'Date ascending',
      'About': "About",
      'Ακολουθήστε ετικέτες': "Subscribe to tags",
      'Ακολουθώντας ετικέτες μπορείτε να ενημερώνεστε για νέες ανακοινώσεις στις ετικέτες που σας ενδιαφέρουν': "By subscribing to specific tags you can get informed about new announcements for the tags you specified",
      'Ανέβασμα αρχείων': "Upload files",
      'Αναζήτηση βάσει tags': "Search tags",
      'Αναζήτηση βάσει κειμένου': "Search text",
      'Αναζήτηση βάσει τίτλου': "Search title",
      'Ανακοινώσεις': "Announcements",
      'Αναφορά προβλήματος': "Report a bug",
      'Αποθήκευση': "Save",
      'Αποσύνδεση': "Logout",
      'Διαχείριση ανακοινώσεων': "Manage announcements",
      'Εισάγετε την τοποθεσία της εκδήλωσης': "Add event location",
      'Εμφάνιση μέχρι': "Show until",
      'Επεξεργασία ανακοίνωσης': "Edit announcement",
      'Επιλέξτε tags': "Choose tags",
      'Επιλέξτε ετικέτες': "Choose tags",
      'Επιλέξτε καθηγητές': "Choose proffesors",
      'Επιλεγμένες ετικέτες': "Chosen tags",
      'Επισήμανση ως σημαντική': "Mark as important",
      'Ετικέτες': "Tags",
      'Ημ/νία έναρξης': "Start date",
      'Ημ/νία λήξης': "End date",
      'Ημερομηνία έως': "Date to",
      'Ημερομηνία από': "Date from",
      'Καθηγητές': "Proffesors",
      'Κείμενο': "Text in greek",
      'Κείμενο στα αγγλικά': "Text in english",
      'Λογαριασμός': "Account",
      'Οι ανακοινώσεις μου': "My announcements",
      'Παρακαλώ περιμένετε': "Please wait",
      'Περιγράψτε συνοπτικά το πρόβλημα καθώς και τα βήματα που ακολουθήσατε': "Please describe the bug and the steps to reproduce",
      'Προσθέστε έναν τίτλο ανακοίνωσης': "Add announcement title",
      'Προσθήκη ανακοίνωσης': "Add announcement",
      'Προσθήκη εκδήλωσης': "Add event",
      'Σύνδεση': "Login",
      'Τίτλος': "Title in greek",
      'Τίτλος στα αγγλικά': "Title in english",
      'Τοποθεσία': "Location",
      'Φίλτρα': "Filters",
      'Φίλτρα αναζήτησης': "Search filters",
      'Επεξεργασία': 'Edit',
      'Συννημένα': 'Attachments'
    },
    el: {
      announcements_page_title: `Ανακοινώσεις - ${config.site_title}`,
      my_announcements_page_title: `Οι ανακοινώσεις μου - ${config.site_title}`,
      account_page_title: `Λογαριασμός - ${config.site_title}`,
      about_page_title: `About - ${config.site_title}`,
    },
  };

  

  setLanguage(languageCode) {
    this.language = languageCode;
    cookieHelper.set('locale', languageCode)
    this.languageChangeFns.forEach((fn) => {
      fn();
    });
  }

  onLanguageChange(fn) {
    this.languageChangeFns.push(fn);

    return () => {
      this.languageChangeFns.splice(this.languageChangeFns.indexOf(fn), 1);
    };
  }

  t(key) {
    // Below code can be used to populate localStorage with all used keys so you can update easily the translates objects
    // UNCOMMENT ONLY IF YOU WANT TO UPDATE TRANSLATES AND ONLY FOR THAT TIME

    // let temp = localStorage.getItem('temp_json_locale') || '{}'
    // temp = JSON.parse(temp)
    // temp[key] = key
    // localStorage.setItem('temp_json_locale', JSON.stringify(temp))
    // console.log(temp)
    return typeof this.translations[this.language][key] !== 'undefined' ? this.translations[this.language][key] : key;
  }

  get_locale_data(data, key) {
    if(this.language == 'en' && data[`eng_${key}`]){
      return data[`eng_${key}`];
    }else{
      return data[key];
    }
  }

  get_site_title() {
    if(this.language == 'en'){
      return config.site_title_en;
    }else{
      return config.site_title;
    }
  }

  onLanguageChangeUnmount(fn) {
    this.languageChangeFns.splice(this.languageChangeFns.indexOf(fn), 1);
  }

  toggle(native, english){
    if(this.language == 'en'){
      return english;
    }else{
      return native;
    }
  }

  greeklish(text) {
    let str = text;
    let greek   = ['α','ά','Ά','Α','β','Β','γ', 'Γ', 'δ','Δ','ε','έ','Ε','Έ','ζ','Ζ','η','ή','Η','θ','Θ','ι','ί','ϊ','ΐ','Ι','Ί', 'κ','Κ','λ','Λ','μ','Μ','ν','Ν','ξ','Ξ','ο','ό','Ο','Ό','π','Π','ρ','Ρ','σ','ς', 'Σ','τ','Τ','υ','ύ','Υ','Ύ','φ','Φ','χ','Χ','ψ','Ψ','ω','ώ','Ω','Ώ',' ',"'","'",',']; 
    let english = ['a', 'a','A','A','b','B','g','G','d','D','e','e','E','E','z','Z','i','i','I','th','Th', 'i','i','i','i','I','I','k','K','l','L','m','M','n','N','x','X','o','o','O','O','p','P' ,'r','R','s','s','S','t','T','u','u','Y','Y','f','F','ch','Ch','ps','Ps','o','o','O','O',' ',' ',' ',',']; 
    
    
    for (let i = 0; i < greek.length; i++) {
      while( str.indexOf(greek[i]) !== -1 && greek[i] != english[i]){
        str = str.replace(greek[i], english[i]);    // CONVERT GREEK CHARACTERS TO LATIN LETTERS
      }
      
    }

    return str;
  }

  get_text(text) {
    if(this.language == 'en'){
      return this.greeklish(text);
    }else{
      return text;
    }
  }
}

const i18n = new I18n();
window.i18n = i18n;

export default i18n;