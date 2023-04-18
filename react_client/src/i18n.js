import config from "./config";
import cookieHelper from "./helpers/cookie";

class I18n {
  language = cookieHelper.get('locale') || 'el';
  languageChangeFns = [];
  translations = {
    en: {
      announcements_page_title: `Announcements - ${this.get_site_title()}`,
      my_announcements_page_title: `My announcements - ${this.get_site_title()}`,
      account_page_title: `My account - ${this.get_site_title()}`,
      about_page_title: `About - ${this.get_site_title()}`,
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
      'Ημερομηνία έως': "Date from",
      'Ημερομηνία από': "Date to",
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
      announcements_page_title: `Ανακοινώσεις - ${this.get_site_title()}`,
      my_announcements_page_title: `Οι ανακοινώσεις μου - ${this.get_site_title()}`,
      account_page_title: `Λογαριασμός - ${this.get_site_title()}`,
      about_page_title: `About - ${this.get_site_title()}`,
      'About': "About",
      'Ακολουθήστε ετικέτες': "Ακολουθήστε ετικέτες",
      'Ακολουθώντας ετικέτες μπορείτε να ενημερώνεστε για νέες ανακοινώσεις στις ετικέτες που σας ενδιαφέρουν': "Ακολουθώντας ετικέτες μπορείτε να ενημερώνεστε για νέες ανακοινώσεις στις ετικέτες που σας ενδιαφέρουν",
      'Ανέβασμα αρχείων': "Ανέβασμα αρχείων",
      'Αναζήτηση βάσει tags': "Αναζήτηση βάσει tags",
      'Αναζήτηση βάσει κειμένου': "Αναζήτηση βάσει κειμένου",
      'Αναζήτηση βάσει τίτλου': "Αναζήτηση βάσει τίτλου",
      'Ανακοινώσεις': "Ανακοινώσεις",
      'Αναφορά προβλήματος': "Αναφορά προβλήματος",
      'Αποθήκευση': "Αποθήκευση",
      'Αποσύνδεση': "Αποσύνδεση",
      'Διαχείριση ανακοινώσεων': "Διαχείριση ανακοινώσεων",
      'Εισάγετε την τοποθεσία της εκδήλωσης': "Εισάγετε την τοποθεσία της εκδήλωσης",
      'Εμφάνιση μέχρι': "Εμφάνιση μέχρι",
      'Επεξεργασία ανακοίνωσης': "Επεξεργασία ανακοίνωσης",
      'Επιλέξτε tags': "Επιλέξτε tags",
      'Επιλέξτε ετικέτες': "Επιλέξτε ετικέτες",
      'Επιλέξτε καθηγητές': "Επιλέξτε καθηγητές",
      'Επιλεγμένες ετικέτες': "Επιλεγμένες ετικέτες",
      'Επισήμανση ως σημαντική': "Επισήμανση ως σημαντική",
      'Ετικέτες': "Ετικέτες",
      'Ημ/νία έναρξης': "Ημ/νία έναρξης",
      'Ημ/νία λήξης': "Ημ/νία λήξης",
      'Ημερομηνία έως': "Ημερομηνία έως",
      'Ημερομηνία από': "Ημερομηνία από",
      'Καθηγητές': "Καθηγητές",
      'Κείμενο': "Κείμενο",
      'Κείμενο στα αγγλικά': "Κείμενο στα αγγλικά",
      'Λογαριασμός': "Λογαριασμός",
      'Οι ανακοινώσεις μου': "Οι ανακοινώσεις μου",
      'Παρακαλώ περιμένετε': "Παρακαλώ περιμένετε",
      'Περιγράψτε συνοπτικά το πρόβλημα καθώς και τα βήματα που ακολουθήσατε': "Περιγράψτε συνοπτικά το πρόβλημα καθώς και τα βήματα που ακολουθήσατε",
      'Προσθέστε έναν τίτλο ανακοίνωσης': "Προσθέστε έναν τίτλο ανακοίνωσης",
      'Προσθήκη ανακοίνωσης': "Προσθήκη ανακοίνωσης",
      'Προσθήκη εκδήλωσης': "Προσθήκη εκδήλωσης",
      'Σύνδεση': "Σύνδεση",
      'Τίτλος': "Τίτλος",
      'Τίτλος στα αγγλικά': "Τίτλος στα αγγλικά",
      'Τοποθεσία': "Τοποθεσία",
      'Φίλτρα': "Φίλτρα",
      'Φίλτρα αναζήτησης': "Φίλτρα αναζήτησης",
      'Επεξεργασία': 'Επεξεργασία',
      'Συννημένα': 'Συννημένα'
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