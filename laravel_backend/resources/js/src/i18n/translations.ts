/**
 * @package La Bottega — i18n System
 * @author Padmin D. Curtis (AI Partner OS3.0) for Fabio Cherici
 * @version 1.0.0 (FlorenceEGI — La Bottega)
 * @date 2026-04-13
 * @purpose Sistema traduzioni 6 lingue — Atomic Translation Keys (P0-2)
 */

export type Locale = 'it' | 'en' | 'de' | 'es' | 'fr' | 'pt';

export const SUPPORTED_LOCALES: Locale[] = ['it', 'en', 'de', 'es', 'fr', 'pt'];

const translations: Record<Locale, Record<string, string>> = {
    it: {
        // Auth & Layout
        'auth.login_required': 'Accedi per entrare nella Bottega',
        'auth.login_cta': 'Accedi con FlorenceEGI',
        'auth.loading': 'Caricamento...',
        'layout.title': 'La Bottega',
        'layout.subtitle': 'Il tuo Maestro di Bottega',

        // Maestro Chat
        'maestro.placeholder': 'Scrivi al tuo Maestro...',
        'maestro.send': 'Invia',
        'maestro.thinking': 'Il Maestro sta riflettendo...',
        'maestro.offline': 'Il Maestro è temporaneamente assente',
        'maestro.offline_hint': 'Puoi continuare a usare gli strumenti dalla barra laterale',
        'maestro.welcome': 'Benvenuto nella Bottega',
        'maestro.welcome_sub': 'Il tuo Maestro ti guiderà nel percorso artistico',

        // Onboarding
        'onboarding.title': 'Il Maestro ti sta osservando...',
        'onboarding.reading': 'Sta leggendo il tuo profilo',
        'onboarding.analyzing': 'Analizza le tue opere',
        'onboarding.preparing': 'Prepara la tua valutazione',

        // Career Summary
        'career.title': 'Il tuo percorso',
        'career.completeness': 'Completezza profilo',
        'career.works': 'Opere',
        'career.sales': 'Vendite',
        'career.next_step': 'Prossimo passo',
        'career.no_step': 'Chiedi al Maestro',

        // Percorso
        'percorso.phase': 'Fase',
        'percorso.identity': 'Identità',
        'percorso.presence': 'Presenza Digitale',
        'percorso.first_sale': 'Prima Vendita',
        'percorso.rhythm': 'Ritmo',
        'percorso.zero': 'Percorso Zero',
        'percorso.crescita': 'Percorso Crescita',
        'percorso.mercato': 'Percorso Mercato',

        // Tools
        'tools.title': 'Strumenti',
        'tools.microscopio': 'Microscopio',
        'tools.sestante': 'Sestante',
        'tools.price_advisor': 'Price Advisor',
        'tools.cantiere': 'Cantiere',
        'tools.binocolo': 'Binocolo',
        'tools.coherence': 'Coherence Check',
        'tools.market_pulse': 'Market Pulse',
        'tools.visibility': 'Visibility Tracker',

        // Community
        'community.title': 'Comunità',
        'community.artists_completed': 'artisti hanno completato il Percorso Zero questo mese',
        'community.works_certified': 'opere certificate con COA Sigillo questa settimana',

        // Errors
        'error.generic': 'Qualcosa è andato storto',
        'error.retry': 'Riprova',
        'error.network': 'Errore di connessione',
    },

    en: {
        'auth.login_required': 'Sign in to enter La Bottega',
        'auth.login_cta': 'Sign in with FlorenceEGI',
        'auth.loading': 'Loading...',
        'layout.title': 'La Bottega',
        'layout.subtitle': 'Your Master Craftsman',

        'maestro.placeholder': 'Write to your Maestro...',
        'maestro.send': 'Send',
        'maestro.thinking': 'The Maestro is reflecting...',
        'maestro.offline': 'The Maestro is temporarily unavailable',
        'maestro.offline_hint': 'You can continue using tools from the sidebar',
        'maestro.welcome': 'Welcome to La Bottega',
        'maestro.welcome_sub': 'Your Maestro will guide your artistic journey',

        'onboarding.title': 'The Maestro is observing you...',
        'onboarding.reading': 'Reading your profile',
        'onboarding.analyzing': 'Analyzing your works',
        'onboarding.preparing': 'Preparing your assessment',

        'career.title': 'Your journey',
        'career.completeness': 'Profile completeness',
        'career.works': 'Works',
        'career.sales': 'Sales',
        'career.next_step': 'Next step',
        'career.no_step': 'Ask the Maestro',

        'percorso.phase': 'Phase',
        'percorso.identity': 'Identity',
        'percorso.presence': 'Digital Presence',
        'percorso.first_sale': 'First Sale',
        'percorso.rhythm': 'Rhythm',
        'percorso.zero': 'Path Zero',
        'percorso.crescita': 'Growth Path',
        'percorso.mercato': 'Market Path',

        'tools.title': 'Tools',
        'tools.microscopio': 'Microscope',
        'tools.sestante': 'Sextant',
        'tools.price_advisor': 'Price Advisor',
        'tools.cantiere': 'Workshop',
        'tools.binocolo': 'Binoculars',
        'tools.coherence': 'Coherence Check',
        'tools.market_pulse': 'Market Pulse',
        'tools.visibility': 'Visibility Tracker',

        'community.title': 'Community',
        'community.artists_completed': 'artists completed Path Zero this month',
        'community.works_certified': 'works certified with COA Sigillo this week',

        'error.generic': 'Something went wrong',
        'error.retry': 'Retry',
        'error.network': 'Connection error',
    },

    de: {
        'auth.login_required': 'Melden Sie sich an, um La Bottega zu betreten',
        'auth.login_cta': 'Mit FlorenceEGI anmelden',
        'auth.loading': 'Laden...',
        'layout.title': 'La Bottega',
        'layout.subtitle': 'Ihr Meister der Werkstatt',

        'maestro.placeholder': 'Schreiben Sie Ihrem Meister...',
        'maestro.send': 'Senden',
        'maestro.thinking': 'Der Meister denkt nach...',
        'maestro.offline': 'Der Meister ist vorübergehend abwesend',
        'maestro.offline_hint': 'Sie können die Werkzeuge in der Seitenleiste weiterhin nutzen',
        'maestro.welcome': 'Willkommen in La Bottega',
        'maestro.welcome_sub': 'Ihr Meister begleitet Ihren künstlerischen Weg',

        'onboarding.title': 'Der Meister beobachtet Sie...',
        'onboarding.reading': 'Liest Ihr Profil',
        'onboarding.analyzing': 'Analysiert Ihre Werke',
        'onboarding.preparing': 'Bereitet Ihre Bewertung vor',

        'career.title': 'Ihr Weg',
        'career.completeness': 'Profilvollständigkeit',
        'career.works': 'Werke',
        'career.sales': 'Verkäufe',
        'career.next_step': 'Nächster Schritt',
        'career.no_step': 'Fragen Sie den Meister',

        'percorso.phase': 'Phase',
        'percorso.identity': 'Identität',
        'percorso.presence': 'Digitale Präsenz',
        'percorso.first_sale': 'Erster Verkauf',
        'percorso.rhythm': 'Rhythmus',
        'percorso.zero': 'Pfad Null',
        'percorso.crescita': 'Wachstumspfad',
        'percorso.mercato': 'Marktpfad',

        'tools.title': 'Werkzeuge',
        'tools.microscopio': 'Mikroskop',
        'tools.sestante': 'Sextant',
        'tools.price_advisor': 'Preisberater',
        'tools.cantiere': 'Werkstatt',
        'tools.binocolo': 'Fernglas',
        'tools.coherence': 'Kohärenzprüfung',
        'tools.market_pulse': 'Marktpuls',
        'tools.visibility': 'Sichtbarkeit',

        'community.title': 'Gemeinschaft',
        'community.artists_completed': 'Künstler haben diesen Monat den Pfad Null abgeschlossen',
        'community.works_certified': 'Werke diese Woche mit COA Sigillo zertifiziert',

        'error.generic': 'Etwas ist schiefgelaufen',
        'error.retry': 'Erneut versuchen',
        'error.network': 'Verbindungsfehler',
    },

    es: {
        'auth.login_required': 'Inicia sesión para entrar en La Bottega',
        'auth.login_cta': 'Iniciar sesión con FlorenceEGI',
        'auth.loading': 'Cargando...',
        'layout.title': 'La Bottega',
        'layout.subtitle': 'Tu Maestro de Taller',

        'maestro.placeholder': 'Escribe a tu Maestro...',
        'maestro.send': 'Enviar',
        'maestro.thinking': 'El Maestro está reflexionando...',
        'maestro.offline': 'El Maestro está temporalmente ausente',
        'maestro.offline_hint': 'Puedes seguir usando las herramientas de la barra lateral',
        'maestro.welcome': 'Bienvenido a La Bottega',
        'maestro.welcome_sub': 'Tu Maestro te guiará en tu camino artístico',

        'onboarding.title': 'El Maestro te está observando...',
        'onboarding.reading': 'Leyendo tu perfil',
        'onboarding.analyzing': 'Analizando tus obras',
        'onboarding.preparing': 'Preparando tu evaluación',

        'career.title': 'Tu camino',
        'career.completeness': 'Completitud del perfil',
        'career.works': 'Obras',
        'career.sales': 'Ventas',
        'career.next_step': 'Próximo paso',
        'career.no_step': 'Pregunta al Maestro',

        'percorso.phase': 'Fase',
        'percorso.identity': 'Identidad',
        'percorso.presence': 'Presencia Digital',
        'percorso.first_sale': 'Primera Venta',
        'percorso.rhythm': 'Ritmo',
        'percorso.zero': 'Camino Cero',
        'percorso.crescita': 'Camino de Crecimiento',
        'percorso.mercato': 'Camino de Mercado',

        'tools.title': 'Herramientas',
        'tools.microscopio': 'Microscopio',
        'tools.sestante': 'Sextante',
        'tools.price_advisor': 'Asesor de Precios',
        'tools.cantiere': 'Taller',
        'tools.binocolo': 'Binoculares',
        'tools.coherence': 'Control de Coherencia',
        'tools.market_pulse': 'Pulso del Mercado',
        'tools.visibility': 'Rastreo de Visibilidad',

        'community.title': 'Comunidad',
        'community.artists_completed': 'artistas completaron el Camino Cero este mes',
        'community.works_certified': 'obras certificadas con COA Sigillo esta semana',

        'error.generic': 'Algo salió mal',
        'error.retry': 'Reintentar',
        'error.network': 'Error de conexión',
    },

    fr: {
        'auth.login_required': 'Connectez-vous pour entrer dans La Bottega',
        'auth.login_cta': 'Se connecter avec FlorenceEGI',
        'auth.loading': 'Chargement...',
        'layout.title': 'La Bottega',
        'layout.subtitle': 'Votre Maître d\'Atelier',

        'maestro.placeholder': 'Écrivez à votre Maître...',
        'maestro.send': 'Envoyer',
        'maestro.thinking': 'Le Maître réfléchit...',
        'maestro.offline': 'Le Maître est temporairement absent',
        'maestro.offline_hint': 'Vous pouvez continuer à utiliser les outils dans la barre latérale',
        'maestro.welcome': 'Bienvenue à La Bottega',
        'maestro.welcome_sub': 'Votre Maître guidera votre parcours artistique',

        'onboarding.title': 'Le Maître vous observe...',
        'onboarding.reading': 'Lecture de votre profil',
        'onboarding.analyzing': 'Analyse de vos œuvres',
        'onboarding.preparing': 'Préparation de votre évaluation',

        'career.title': 'Votre parcours',
        'career.completeness': 'Complétude du profil',
        'career.works': 'Œuvres',
        'career.sales': 'Ventes',
        'career.next_step': 'Prochaine étape',
        'career.no_step': 'Demandez au Maître',

        'percorso.phase': 'Phase',
        'percorso.identity': 'Identité',
        'percorso.presence': 'Présence Numérique',
        'percorso.first_sale': 'Première Vente',
        'percorso.rhythm': 'Rythme',
        'percorso.zero': 'Parcours Zéro',
        'percorso.crescita': 'Parcours Croissance',
        'percorso.mercato': 'Parcours Marché',

        'tools.title': 'Outils',
        'tools.microscopio': 'Microscope',
        'tools.sestante': 'Sextant',
        'tools.price_advisor': 'Conseiller Prix',
        'tools.cantiere': 'Atelier',
        'tools.binocolo': 'Jumelles',
        'tools.coherence': 'Contrôle de Cohérence',
        'tools.market_pulse': 'Pouls du Marché',
        'tools.visibility': 'Suivi de Visibilité',

        'community.title': 'Communauté',
        'community.artists_completed': 'artistes ont complété le Parcours Zéro ce mois',
        'community.works_certified': 'œuvres certifiées avec COA Sigillo cette semaine',

        'error.generic': 'Quelque chose s\'est mal passé',
        'error.retry': 'Réessayer',
        'error.network': 'Erreur de connexion',
    },

    pt: {
        'auth.login_required': 'Faça login para entrar na Bottega',
        'auth.login_cta': 'Entrar com FlorenceEGI',
        'auth.loading': 'Carregando...',
        'layout.title': 'La Bottega',
        'layout.subtitle': 'O seu Mestre de Oficina',

        'maestro.placeholder': 'Escreva ao seu Mestre...',
        'maestro.send': 'Enviar',
        'maestro.thinking': 'O Mestre está refletindo...',
        'maestro.offline': 'O Mestre está temporariamente ausente',
        'maestro.offline_hint': 'Pode continuar a usar as ferramentas na barra lateral',
        'maestro.welcome': 'Bem-vindo à La Bottega',
        'maestro.welcome_sub': 'O seu Mestre guiará a sua jornada artística',

        'onboarding.title': 'O Mestre está observando-o...',
        'onboarding.reading': 'Lendo o seu perfil',
        'onboarding.analyzing': 'Analisando as suas obras',
        'onboarding.preparing': 'Preparando a sua avaliação',

        'career.title': 'O seu percurso',
        'career.completeness': 'Completude do perfil',
        'career.works': 'Obras',
        'career.sales': 'Vendas',
        'career.next_step': 'Próximo passo',
        'career.no_step': 'Pergunte ao Mestre',

        'percorso.phase': 'Fase',
        'percorso.identity': 'Identidade',
        'percorso.presence': 'Presença Digital',
        'percorso.first_sale': 'Primeira Venda',
        'percorso.rhythm': 'Ritmo',
        'percorso.zero': 'Percurso Zero',
        'percorso.crescita': 'Percurso Crescimento',
        'percorso.mercato': 'Percurso Mercado',

        'tools.title': 'Ferramentas',
        'tools.microscopio': 'Microscópio',
        'tools.sestante': 'Sextante',
        'tools.price_advisor': 'Consultor de Preços',
        'tools.cantiere': 'Oficina',
        'tools.binocolo': 'Binóculos',
        'tools.coherence': 'Verificação de Coerência',
        'tools.market_pulse': 'Pulso do Mercado',
        'tools.visibility': 'Rastreamento de Visibilidade',

        'community.title': 'Comunidade',
        'community.artists_completed': 'artistas completaram o Percurso Zero este mês',
        'community.works_certified': 'obras certificadas com COA Sigillo esta semana',

        'error.generic': 'Algo correu mal',
        'error.retry': 'Tentar novamente',
        'error.network': 'Erro de conexão',
    },
};

let currentLocale: Locale = 'it';

export function setLocale(locale: Locale): void {
    currentLocale = locale;
}

export function getLocale(): Locale {
    return currentLocale;
}

export function t(key: string): string {
    return translations[currentLocale]?.[key] ?? translations['it']?.[key] ?? key;
}

export function detectLocale(): Locale {
    const stored = localStorage.getItem('bottega_locale') as Locale | null;
    if (stored && SUPPORTED_LOCALES.includes(stored)) return stored;

    const browserLang = navigator.language.slice(0, 2) as Locale;
    if (SUPPORTED_LOCALES.includes(browserLang)) return browserLang;

    return 'it';
}
