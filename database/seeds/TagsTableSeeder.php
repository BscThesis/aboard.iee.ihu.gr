<?php

use Illuminate\Database\Seeder;
use App\Tag;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data = '[ { "title": "Όλες οι ανακοινώσεις", "parent_id": null, "is_public": 0 }, { "title": "Eξάμηνο Α", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο Β", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο Γ", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο Δ", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο Ε", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο ΣΤ", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο Ζ", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο Η", "parent_id": 1, "is_public": 0 }, { "title": "Eξάμηνο Θ", "parent_id": 1, "is_public": 0 }, { "title": "Πρακτική άσκηση", "parent_id": 1, "is_public": 0 }, { "title": "Πτυχιακή εργασία", "parent_id": 1, "is_public": 0 }, { "title": "Παρουσιάσεις πτυχιακών", "parent_id": 1, "is_public": 0 }, { "title": "Erasmus", "parent_id": 1, "is_public": 1 }, { "title": "Νέα τμήματος", "parent_id": 1, "is_public": 1 }, { "title": "Τεχνικά θέματα", "parent_id": 1, "is_public": 1 }, { "title": "Μεταπτυχιακά", "parent_id": 1, "is_public": 1 }, { "title": "IEEE Student Branch", "parent_id": 1, "is_public": 1 }, { "title": "Άλλα δημόσια νέα", "parent_id": 1, "is_public": 1 }, { "title": "Ερευνητικά προγράμματα", "parent_id": 1, "is_public": 1 }, { "title": "Ανακοινώσεις γραμματείας", "parent_id": 1, "is_public": 1 }, { "title": "Εκδηλώσεις", "parent_id": 1, "is_public": 1 }, { "title": "ΠΜΣ Ευφυείς Τεχνολογίες Διαδικτύου", "parent_id": 1, "is_public": 1 }, { "title": "Διακηρύξεις - Προκηρύξεις", "parent_id": 1, "is_public": 1 } ] ';

        $tags = json_decode($data, true);

        foreach($tags as $item) {
            Tag::create([
                'title' => $item['title'],
                'parent_id' => $item['parent_id'],
                'is_public' => $item['is_public']
            ]);
        }

        $data = '[{"title":"1101 Μαθηματικά Ι","is_public":0,"parent_id":2},{"title":"1102 Δομημένος Προγραμματισμός","is_public":0,"parent_id":2},{"title":"1103 Εισαγωγή στην Επιστήμη των Υπολογιστών","is_public":0,"parent_id":2},{"title":"1104 Ηλεκτρονική Φυσική","is_public":0,"parent_id":2},{"title":"1105 Κυκλώματα Συνεχούς Ρεύματος","is_public":0,"parent_id":2},{"title":"1201 Μαθηματικά ΙΙ","is_public":0,"parent_id":3},{"title":"1202 Μετρήσεις και Κυκλώματα Εναλλασσόμενου Ρεύματος","is_public":0,"parent_id":3},{"title":"1203 Τεχνική Συγγραφή,0, Παρουσίαση και Ορολογία Ξένης Γλώσσας","is_public":0,"parent_id":3},{"title":"1204 Σχεδίαση Ψηφιακών Συστημάτων","is_public":0,"parent_id":3},{"title":"1205 Αντικειμενοστρεφής Προγραμματισμός","is_public":0,"parent_id":3},{"title":"1301 Θεωρία Πιθανοτήτων και Στατιστική","is_public":0,"parent_id":4},{"title":"1302 Μαθηματικά ΙΙI","is_public":0,"parent_id":4},{"title":"1303 Επεξεργασία Σήματος","is_public":0,"parent_id":4},{"title":"1304 Οργάνωση και Αρχιτεκτονική Υπολογιστικών Συστημάτων","is_public":0,"parent_id":4},{"title":"1305 Δομές Δεδομένων και Ανάλυση Αλγορίθμων","is_public":0,"parent_id":4},{"title":"1401 Συστήματα Διαχείρισης Βάσεων Δεδομένων","is_public":0,"parent_id":5},{"title":"1402 Τηλεπικοινωνιακά Συστήματα","is_public":0,"parent_id":5},{"title":"1403 Εισαγωγή στα Λειτουργικά Συστήματα","is_public":0,"parent_id":5},{"title":"1404 Ηλεκτρονικά Κυκλώματα","is_public":0,"parent_id":5},{"title":"1405 Γλώσσες και Τεχνολογίες Ιστού","is_public":0,"parent_id":5},{"title":"1501 Ασύρματες Επικοινωνίες","is_public":0,"parent_id":6},{"title":"1502 Μικροελεγκτές","is_public":0,"parent_id":6},{"title":"1503 Σχεδίαση Λειτουργικών Συστημάτων","is_public":0,"parent_id":6},{"title":"1504 Ηλεκτρονικές Διατάξεις","is_public":0,"parent_id":6},{"title":"1505 Αλληλεπίδραση Ανθρώπου-Μηχανής","is_public":0,"parent_id":6},{"title":"1601 Τεχνητή Νοημοσύνη","is_public":0,"parent_id":7},{"title":"1602 Ενσωματωμένα Συστήματα","is_public":0,"parent_id":7},{"title":"1611 Σύνθεση Ηλεκτρονικών Κυκλωμάτων","is_public":0,"parent_id":7},{"title":"1612 Κβαντική Υπολογιστική","is_public":0,"parent_id":7},{"title":"1613 Μεθοδολογίες Σχεδιασμού Μικροηλεκτρονικών Κυκλωμάτων **","is_public":0,"parent_id":7},{"title":"1641 Αριθμητικές Μέθοδοι","is_public":0,"parent_id":7},{"title":"1642 Προηγμένα Θέματα Αλληλεπίδρασης (Προγραμματισμός Κινητών Συσκευών)","is_public":0,"parent_id":7},{"title":"1643 Διοίκηση Έργων","is_public":0,"parent_id":7},{"title":"1671 Μικροκυματική Τεχνολογία και Τηλεπισκόπηση","is_public":0,"parent_id":7},{"title":"1672 Οπτοηλεκτρονική και Οπτικές Επικοινωνίες","is_public":0,"parent_id":7},{"title":"1673 Συστήματα Μέσων Μαζικής Επικοινωνίας","is_public":0,"parent_id":7},{"title":"1701 Δίκτυα Υπολογιστών","is_public":0,"parent_id":8},{"title":"1702 Ηλεκτρονικά Ισχύος","is_public":0,"parent_id":8},{"title":"1711 Συστήματα Αυτομάτου Ελέγχου","is_public":0,"parent_id":8},{"title":"1712 Αισθητήρια και Επεξεργασία Μετρήσεων","is_public":0,"parent_id":8},{"title":"1713 Προγραμματιζόμενοι Λογικοί Ελεγκτές","is_public":0,"parent_id":8},{"title":"1714 Σχεδίαση Επαναπροσδιοριζόμενων Ψηφιακών Συστημάτων (FPGA)","is_public":0,"parent_id":8},{"title":"1741 Εισαγωγή στην Αναλυτική των Δεδομένων","is_public":0,"parent_id":8},{"title":"1742 Μηχανική Λογισμικού","is_public":0,"parent_id":8},{"title":"1743 Τεχνολογία Βάσεων Δεδομένων","is_public":0,"parent_id":8},{"title":"1744 Προηγμένες Αρχιτεκτονικές Υπολογιστών και Προγραμματισμός Παράλληλων Συστημάτων","is_public":0,"parent_id":8},{"title":"1771 Τεχνολογίες Ήχου και Εικόνας","is_public":0,"parent_id":8},{"title":"1801 Ασφάλεια Πληροφοριακών Συστημάτων","is_public":0,"parent_id":9},{"title":"1802 Αρχές και Μέθοδοι Μηχανικής Μάθησης","is_public":0,"parent_id":9},{"title":"1803 Διαδίκτυο των Πραγμάτων","is_public":0,"parent_id":9},{"title":"1811 Εφαρμογές Συστημάτων Αυτομάτου Ελέγχου","is_public":0,"parent_id":9},{"title":"1812 Μετατροπείς Ισχύος","is_public":0,"parent_id":9},{"title":"1837 Μικροηλεκτρονική","is_public":0,"parent_id":9},{"title":"1838 Εφαρμογές Συστημάτων Ισχύος και ΑΠΕ","is_public":0,"parent_id":9},{"title":"1839 Ηλεκτροκίνηση και Ευφυή Δίκτυα","is_public":0,"parent_id":9},{"title":"1841 Οργάνωση Δεδομένων και Εξόρυξη Πληροφορίας","is_public":0,"parent_id":9},{"title":"1842 Διαδικτυακές Υπηρεσίες Προστιθέμενης Αξίας","is_public":0,"parent_id":9},{"title":"1871 Ασύρματα Δίκτυα","is_public":0,"parent_id":9},{"title":"1872 Ειδικά Θέματα Δικτύων (CCNA) 1","is_public":0,"parent_id":9},{"title":"1873 Προηγμένα Θέματα Δικτύων","is_public":0,"parent_id":9},{"title":"1874 Συστήματα Κινητών Επικοινωνιών","is_public":0,"parent_id":9},{"title":"1899 Ραδιοτηλεοπτική Παραγωγή","is_public":0,"parent_id":9},{"title":"1911 Εφαρμογές Ενσωματωμένων Συστημάτων","is_public":0,"parent_id":10},{"title":"1912 Ρομποτική","is_public":0,"parent_id":10},{"title":"1913 ΑΠΕ και Ευφυή Ηλεκτρικά Δίκτυα","is_public":0,"parent_id":10},{"title":"1914 Απτικές Διεπαφές","is_public":0,"parent_id":10},{"title":"1915 Βιοϊατρική Τεχνολογία","is_public":0,"parent_id":10},{"title":"1916 Συστήματα Μετρήσεων Υποβοηθούμενων από Η/Υ","is_public":0,"parent_id":10},{"title":"1941 Ανάπτυξη Διαδικτυακών Συστημάτων και Εφαρμογών","is_public":0,"parent_id":10},{"title":"1942 Επιχειρησιακή Έρευνα","is_public":0,"parent_id":10},{"title":"1943 Ανάκτηση Πληροφοριών - Μηχανές Αναζήτησης","is_public":0,"parent_id":10},{"title":"1944 Διαxείριση Συστήματος και Υπηρεσιών DBMS","is_public":0,"parent_id":10},{"title":"1945 Ευφυή Συστήματα","is_public":0,"parent_id":10},{"title":"1946 Προηγμένα Θέματα Τεχνητής Νοημοσύνης","is_public":0,"parent_id":10},{"title":"1947 Προηγμένη Μηχανική Μάθηση","is_public":0,"parent_id":10},{"title":"1948 Ανάπτυξη Ολοκληρωμένων Πληροφοριακών Συστημάτων","is_public":0,"parent_id":10},{"title":"1949 Κατανεμημένα Συστήματα","is_public":0,"parent_id":10},{"title":"1950 Σημασιολογικός Ιστός","is_public":0,"parent_id":10},{"title":"1969 Γραφικά Υπολογιστών","is_public":0,"parent_id":10},{"title":"1971 Ασφάλεια Δικτύων και Επικοινωνιών","is_public":0,"parent_id":10},{"title":"1972 Δικτύωση Καθορισμένη από Λογισμικό","is_public":0,"parent_id":10},{"title":"1973 Ειδικά Θέματα Δικτύων (CCNA) 2","is_public":0,"parent_id":10},{"title":"1974 Δορυφορικές Επικοινωνίες","is_public":0,"parent_id":10},{"title":"1975 Τεχνολογία Πολυμέσων","is_public":0,"parent_id":10}]';

        $tags = json_decode($data, true);

        foreach($tags as $item) {
            Tag::create([
                'title' => $item['title'],
                'parent_id' => $item['parent_id'],
                'is_public' => $item['is_public']
            ]);
        }
    }
}

