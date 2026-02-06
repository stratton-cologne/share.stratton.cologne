import { createI18n } from "vue-i18n";

const messages = {
    de: {
        legal: {
            label: "Rechtliches",
        },
        nav: {
            upload: "Upload",
            progress: "Progress",
            impressum: "Impressum",
            datenschutz: "Datenschutz",
            agb: "AGB",
            admin: "Admin",
            themeLight: "Hellmodus",
            themeDark: "Dunkelmodus",
        },
        footer: {
            copyright: "© 2026 Stratton Cologne",
            cookieSettings: "Cookie-Einstellungen",
            secureShare: "Sicheres Teilen großer Dateien",
        },
        upload: {
            title: "Datei hochladen",
            subtitle:
                "Teile große Dateien sicher und schnell. Nach dem Upload erhältst du einen Link zum Teilen.",
            selectFiles: "Dateien auswählen",
            dragDrop: "Drag & Drop oder klick hier",
            maxFile: "Maximal 1 GB pro Datei (je nach Server-Config)",
            selectedFiles: "Ausgewählte Dateien",
            filesSelected: "{count} Datei(en)",
            uploading: "Upload läuft",
            emailLabel: "E-Mail (Pflicht)",
            emailPlaceholder: "z. B. {email}",
            emailHint:
                "Wir senden dir eine Erinnerung, bevor die Datei gelöscht wird.",
            expiresLabel: "Ablaufzeit (Tage)",
            expiresPlaceholder: "z. B. 7",
            maxDownloadsLabel: "Max. Downloads",
            downloadsPlaceholder: "z. B. 50",
            startUpload: "Upload starten",
            stepsTitle: "So funktioniert's",
            steps: {
                pick: "Datei auswählen und hochladen.",
                share: "Share-Link kopieren und teilen.",
                download: "Empfänger lädt die Datei sicher herunter.",
            },
            controlTitle: "Mehr Kontrolle",
            controlText:
                "Definiere optional eine Ablaufzeit oder ein Download-Limit für deinen Share-Link.",
            agbAccept: "Ich akzeptiere die",
            agbButton: "AGB",
            agbModalTitle: "AGB / Nutzungsbedingungen",
            agbModalIntro:
                "Diese Nutzungsbedingungen gelten für die Nutzung des File‑Sharing‑Dienstes Stratton Share.",
            agbModalIllegal:
                "Es ist untersagt, illegale oder verbotene Inhalte hochzuladen oder zu teilen. Ebenso ist das Hochladen, Teilen oder Verbreiten von urheberrechtsverletzendem Material nicht erlaubt. Du bist dafür verantwortlich, dass alle hochgeladenen Inhalte rechtmäßig sind und keine Rechte Dritter verletzen.",
            agbModalEnforcement:
                "Bei Verstößen gegen diese Bedingungen können Inhalte gesperrt oder gelöscht werden. Bei schweren oder wiederholten Verstößen kann der Zugang dauerhaft gesperrt werden.",
            agbModalContact: "Kontakt: {email}",
            close: "Schließen",
            understood: "Verstanden",
        },
        privacy: {
            title: "Datenschutzerklärung",
            subtitle: "Informationen zum Umgang mit personenbezogenen Daten",
            section1Title: "1. Verantwortliche Stelle",
            section1Body:
                "Stratton Cologne GmbH, Hohenzollernring 123, 50672 Köln",
            section2Title: "2. Verarbeitung im Rahmen des Dienstes",
            section2Body:
                "Beim Hochladen von Dateien speichern wir die bereitgestellten Inhalte sowie Metadaten wie Dateiname, Dateigröße, Upload-Zeitpunkt, Ablaufdatum und Download-Zähler. Zusätzlich verarbeiten wir die beim Upload angegebene E-Mail-Adresse, um dich vor der automatischen Löschung zu informieren. Die Verarbeitung erfolgt ausschließlich zur Bereitstellung des File-Sharing-Dienstes.",
            section2Note:
                "Bitte lade keine rechtswidrigen oder verbotenen Inhalte hoch und teile kein urheberrechtsverletzendes Material.",
            section3Title: "3. Speicherung & Löschung",
            section3Body:
                "Dateien werden nach Ablaufdatum oder nach Erreichen des Download-Limits automatisch entfernt. Vor der Löschung senden wir Erinnerungs-E-Mails (z. B. 72 und 24 Stunden vorher) sowie eine Bestätigung nach der Löschung. Du kannst die Löschung jederzeit anfordern.",
            section4Title: "4. Cookies",
            section4Body:
                "Wir setzen ausschließlich essenzielle Cookies ein, die für den Betrieb des Dienstes notwendig sind. Eine Einwilligung kannst du über den Cookie-Banner verwalten.",
            section5Title: "5. Betroffenenrechte",
            section5Body:
                "Du hast das Recht auf Auskunft, Berichtigung, Löschung und Einschränkung der Verarbeitung deiner Daten. Kontaktiere uns dazu jederzeit.",
            section6Title: "6. Kontakt Datenschutz",
            section6Body: "E-Mail: {email}",
            disclaimer:
                "Hinweis: Diese Datenschutzerklärung ist eine Vorlage und muss rechtlich geprüft werden.",
        },
        imprint: {
            title: "Impressum",
            subtitle: "Angaben gemäß § 5 TMG",
            providerTitle: "Anbieter",
            providerName: "Simon Marcel Linden",
            providerStreet: "Mathiaskirchplatz 15",
            providerCity: "50968 Köln-Bayenthal",
            contactTitle: "Kontakt",
            contactPhone: "Telefon: +49 176 91460756",
            contactEmail: "E-Mail: {email}",
            representativeTitle: "Vertretungsberechtigt",
            representativeName: "Simon Marcel Linden",
        },
        terms: {
            title: "AGB / Nutzungsbedingungen",
            subtitle: "Regeln für die Nutzung von Stratton Share",
            section1Title: "1. Geltungsbereich",
            section1Body:
                "Diese Nutzungsbedingungen gelten für die Nutzung des File‑Sharing‑Dienstes Stratton Share.",
            section2Title: "2. Leistungen",
            section2Body:
                "Wir stellen eine Plattform zum zeitlich befristeten Teilen von Dateien bereit. Es besteht kein Anspruch auf eine bestimmte Verfügbarkeit oder Speicherdauer.",
            section3Title: "3. Pflichten der Nutzer",
            section3Body:
                "Es ist untersagt, illegale oder verbotene Inhalte hochzuladen oder zu teilen. Ebenso ist das Hochladen, Teilen oder Verbreiten von urheberrechtsverletzendem Material nicht erlaubt. Du bist dafür verantwortlich, dass alle hochgeladenen Inhalte rechtmäßig sind und keine Rechte Dritter verletzen.",
            section4Title: "4. Sperrung von Inhalten",
            section4Body:
                "Bei Verstößen gegen diese Bedingungen können Inhalte gesperrt oder gelöscht werden. Bei schweren oder wiederholten Verstößen kann der Zugang dauerhaft gesperrt werden.",
            section5Title: "5. Haftung",
            section5Body:
                "Wir haften nicht für Inhalte, die von Nutzern hochgeladen werden. Für Schäden haftet Stratton Share nur bei Vorsatz oder grober Fahrlässigkeit.",
            section6Title: "6. Kontakt",
            section6Body: "E-Mail: {email}",
        },
    },
    en: {
        legal: {
            label: "Legal",
        },
        nav: {
            upload: "Upload",
            progress: "Progress",
            impressum: "Imprint",
            datenschutz: "Privacy",
            agb: "Terms",
            admin: "Admin",
            themeLight: "Light mode",
            themeDark: "Dark mode",
        },
        footer: {
            copyright: "© 2026 Stratton Cologne",
            cookieSettings: "Cookie settings",
            secureShare: "Securely share large files",
        },
        upload: {
            title: "Upload file",
            subtitle:
                "Share large files safely and quickly. After the upload you will receive a sharing link.",
            selectFiles: "Select files",
            dragDrop: "Drag & drop or click here",
            maxFile: "Maximum 1 GB per file (depending on server config)",
            selectedFiles: "Selected files",
            filesSelected: "{count} file(s)",
            uploading: "Uploading",
            emailLabel: "Email (required)",
            emailPlaceholder: "e.g. {email}",
            emailHint: "We will remind you before the file is deleted.",
            expiresLabel: "Expiry (days)",
            expiresPlaceholder: "e.g. 7",
            maxDownloadsLabel: "Max downloads",
            downloadsPlaceholder: "e.g. 50",
            startUpload: "Start upload",
            stepsTitle: "How it works",
            steps: {
                pick: "Choose a file and upload it.",
                share: "Copy the share link and send it.",
                download: "Recipient downloads the file securely.",
            },
            controlTitle: "More control",
            controlText:
                "Optionally set an expiry time or a download limit for your share link.",
            agbAccept: "I accept the",
            agbButton: "Terms",
            agbModalTitle: "Terms of Use",
            agbModalIntro: "These terms apply to the use of Stratton Share.",
            agbModalIllegal:
                "Uploading or sharing illegal or prohibited content is not allowed. Sharing copyright‑infringing material is also prohibited. You are responsible for ensuring all uploaded content is lawful and does not violate third‑party rights.",
            agbModalEnforcement:
                "We may block or delete content that violates these terms. Serious or repeated violations may lead to permanent suspension.",
            agbModalContact: "Contact: {email}",
            close: "Close",
            understood: "Got it",
        },
        privacy: {
            title: "Privacy policy",
            subtitle: "Information about how we handle personal data",
            section1Title: "1. Controller",
            section1Body:
                "Stratton Cologne GmbH, Hohenzollernring 123, 50672 Cologne",
            section2Title: "2. Processing within the service",
            section2Body:
                "When you upload files, we store the provided content and metadata such as file name, file size, upload time, expiry date, and download count. We also process the email address provided during upload to notify you before deletion. Processing is solely for providing the file-sharing service.",
            section2Note:
                "Please do not upload illegal or prohibited content or share copyright-infringing material.",
            section3Title: "3. Storage & deletion",
            section3Body:
                "Files are removed automatically after the expiry date or when the download limit is reached. Before deletion we send reminder emails (e.g., 72 and 24 hours before) and a confirmation after deletion. You can request deletion at any time.",
            section4Title: "4. Cookies",
            section4Body:
                "We only use essential cookies necessary to operate the service. You can manage consent via the cookie banner.",
            section5Title: "5. Your rights",
            section5Body:
                "You have the right to access, rectify, delete, and restrict processing of your data. Contact us anytime.",
            section6Title: "6. Privacy contact",
            section6Body: "Email: {email}",
            disclaimer:
                "Note: This privacy policy is a template and must be legally reviewed.",
        },
        imprint: {
            title: "Imprint",
            subtitle: "Information pursuant to § 5 TMG",
            providerTitle: "Provider",
            providerName: "Simon Marcel Linden",
            providerStreet: "Mathiaskirchplatz 15",
            providerCity: "50968 Cologne-Bayenthal",
            contactTitle: "Contact",
            contactPhone: "Phone: +49 176 91460756",
            contactEmail: "Email: {email}",
            representativeTitle: "Represented by",
            representativeName: "Simon Marcel Linden",
        },
        terms: {
            title: "Terms of Use",
            subtitle: "Rules for using Stratton Share",
            section1Title: "1. Scope",
            section1Body:
                "These terms apply to the use of the Stratton Share file‑sharing service.",
            section2Title: "2. Service",
            section2Body:
                "We provide a platform for time‑limited file sharing. There is no entitlement to any specific availability or storage duration.",
            section3Title: "3. User obligations",
            section3Body:
                "Uploading or sharing illegal or prohibited content is not allowed. Sharing copyright‑infringing material is also prohibited. You are responsible for ensuring all uploaded content is lawful and does not violate third‑party rights.",
            section4Title: "4. Blocking content",
            section4Body:
                "We may block or delete content that violates these terms. Serious or repeated violations may lead to permanent suspension.",
            section5Title: "5. Liability",
            section5Body:
                "We are not liable for user‑uploaded content. Stratton Share is only liable for damages in cases of intent or gross negligence.",
            section6Title: "6. Contact",
            section6Body: "Email: {email}",
        },
    },
} as const;

const i18n = createI18n({
    globalInjection: true,
    legacy: false,
    locale: "de",
    fallbackLocale: "en",
    messages,
});

export default i18n;
