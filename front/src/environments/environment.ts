const front_url: any = "http://localhost:4200";
const api_url: any = "http://localhost:8000";
const production:boolean = false;
export const environment = {
  start_year: 2025,
  FRONT_URL: `${front_url}`,
  production: production,
  /* ------------------------------ ajout article ----------------------------- */
  value_to_compress_images: 1000000, // 1 mb
  max_val_images: 10000000, // 10 mb
  max_val_excel: 10000000, // 10 mb
  /* ------------------------------ update image ------------------------------ */
  max_value_to_compress: 1024, // ko = 1 mb
  max_value_to_upload: 10240, // ko = 10 mb
  /* -------------------------------- Les apis -------------------------------- */
  API_BASE_URL_GENERAL: `${api_url}/api/`,
  API_BASE_URL_USER: `${api_url}/api_user/`,
  API_BASE_URL_PARAMETRAGE: `${api_url}/api_parametrage/`,
  API_BASE_URL_CONFIGURATIONS: `${api_url}/api_configurations/`,
  API_BASE_URL_DEVISE: `${api_url}/api_devise/`,
  /* --------------------------------- Claims --------------------------------- */
  unique_claim: "iIsIJleHAiOjEzpKusj76h4PTm0ploK",
  KEY_CRYPTO: "eyJhbGciOiJIUzI1NiIsIJleHAiOjEz81729fza9182jOUcmiWwYrAKt1NjX0.KS3v5g8idzdhzdz81729flucjOUcmiWwYrAKtS0zc8j-g0_HbqVh6U",
  /* --------------------- la listes des bases de données --------------------- */
  liste_databases: [
    { value: "crm_depenses_db", label: "CRM DEPENSES" },
  ],
  liste_multimedias_type: [
    {
      value: 1,
      label: "Image",
      allowed_size: 25, // mb
      max_comporess_value: 1,
      can_compress: true,
      allowed: ".png,.jpg,.jpeg,.gif,.jfif",
    },
    {
      value: 2,
      label: "Audio",
      allowed_size: 2, // mb
      can_compress: false,
      allowed: ".mp3,.ogg,.opus",
    },
    {
      value: 3,
      label: "Video",
      allowed_size: 2, // mb
      can_compress: false,
      allowed: ".mp4,.mov",
    },
    {
      value: 4,
      label: "Fichier",
      can_compress: false,
      allowed: ".json,.xlsx,.pdf,.docx,.txt",
      allowed_size: 2 // mb
    },
  ],
  urlMenuMap: {
    /* --------------------------- Le module d'accueil -------------------------- */
    "/accueil": "/accueil",
    /* ----------------------- Le module gestion de compte ---------------------- */
    "/compte": "/compte",
    /* ---------------------- Le module des configurations ---------------------- */
    "/configurations": "/configurations",
    "/configurations/liste_utilisateurs": "/configurations",
    "/configurations/liste_roles": "/configurations",
    "/configurations/group_role_sittings": "/configurations",
    "/configurations/liste_logs": "/configurations",
    "/configurations/menu": "/configurations",
  },
  api: {
    general: {
      telecharger: "TelechargerDocument",
      LoadParamsForList: "LoadParamsForList",
      AfficherLogs: 'AfficherLogs',
    },
    /* ------------------------ Les apis des utilisateurs ----------------------- */
    user: {
      login: "LoginUtilisateur",
      user_info: "GetMyInfo",
      modifier: "ModifierUtilisateur",
      modifier_password: "ChangePassword",
      afficherimage: "AfficherMonImage",
      supprimerimage: "SupprimerMonImage",
      modifierimage: "ModifierMonImage",
      afficher: "AfficherUtilisateur",
      supprimer: "SupprimerUtilisateur",
      ajouter: "AjouterUtilisateur",
      modifier_params: "ModifierParmsUtilisateur"
    },
    role: {
      afficher: "AfficherRole",
      ajouter: "AjouterRole",
      modfier: "ModifierRole"
    },
    app_parameters: {
      AjouterSidebarButtons: 'AjouterSidebarButtons',
      AfficherSidebarButtons: 'AfficherSidebarButtons',
      ModifierSidebarButtons: 'ModifierSidebarButtons',
      SupprimerSidebarButtons: 'SupprimerSidebarButtons',
      /* ---------------------------------- roles --------------------------------- */
      AjoutGroupRole: "AjoutGroupRole",
      ModifierGroupRole: "ModifierGroupRole",
      AfficherGroupRole: 'AfficherGroupRole'
    },
    devise: {
      AfficherDeviseParameter: "AfficherDeviseParameter",
      AjouterDeviseParameter: "AjouterDeviseParameter",
      ModifierDeviseParameter: "ModifierDeviseParameter",
      SuppressionDeviseParameter: "SuppressionDeviseParameter"
    },
    transaction :{
      AfficherTransaction: "AfficherTransaction",
      AjouterTransaction: "AjouterTransaction",
      ModifierTransaction: "ModifierTransaction",
      SuppressionTransaction: "SuppressionTransaction"
    }
  },
  /* -------------------------------------------------------------------------- */
  /*                                 LES LISTES                                 */
  /* -------------------------------------------------------------------------- */
  liste_type_societe: [
    { value: 1, label: "Particulier" },
    { value: 2, label: "Entreprise" }
  ],
  liste_autorisations: [
    { value: 1, label: "Autorisé", badge: "bg-vert" },
    { value: 2, label: "Non autorisé", badge: "bg-rouge" }
  ],
  style_print: `
  <style type="text/css">
  @font-face {
    font-family: CeraRoundPro;
    src: url(/assets/Font/TypeMatesCeraRoundProRegular.otf) format("opentype");
  }
  * {
  font-family: CeraRoundPro !important;
  }
  .badge {
    display: inline-block;
    font-size: 75%;
    font-weight: bold;
    line-height: 1;
    text-align: center;
    vertical-align: baseline;
    border-radius: 0.25rem;
    padding: 7px;
    color: #00000;
  }
  .bg-jaune {
    background: #feedaf !important;
    color: #8a5340 !important;
  }
  .bg-bleu {
    background-color: #b3e5fc !important;
    color: #23547b !important;
  }
  .bg-vert {
    background-color: #c8e6c9 !important;
    color: #256029 !important;
  }
  .center_media {
    text-align: center !important;
    justify-content: center !important;
  }
  .page-header,
  .page-header-space {
    height: 80px;
  }
  .page-footer,
  .page-footer-space {
    height: 50px;
  }
  table {
    width: 100%;
    max-width: 100%;
    margin-bottom: 1rem;
    background-color: transparent;
    border-collapse: collapse;
  }
  .table thead th {
    vertical-align: bottom;
    border-bottom: 1px solid #dee2e6;
  }
  .table td,
  .table th {
    padding: 7px;
    border: 1px solid #dee2e6;
  }
  th {
    text-align: inherit;
  }
  *,
  ::after,
  ::before {
    box-sizing: border-box;
  }
  .page-footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    border-top: 0.5px solid black; /* for demo */
  }
  .page-header {
    margin-bottom: 5px;
    position: fixed;
    top: 0mm;
    width: 100%;
  }
  .font-weight-bold {
    font-weight: bold;
  }
  .mb-2 {
    margin-bottom: 0.5rem !important;
  }
  .text-center {
    text-align: center !important;
  }
    .text-right {
    text-align: right !important;
  }
  .mb-4 {
    margin-bottom: 1.5rem !important;
  }
  .mt-4 {
    margin-top: 1.5rem !important;
  }
  .d-flex {
    display: flex !important;
  }
  .justify-content-between {
    justify-content: space-between !important;
  }
  .justify-content-end {
    justify-content: flex-end !important;
  }
  .justify-content-start {
    justify-content: flex-start !important;
  }
  ul {
    margin-top: 0;
    margin-bottom: 5px;
    padding-left: 0 !important;
  }
  @page {
    margin: 8mm;
  }
  @media print {
    thead {
      display: table-header-group;
    }
    tfoot {
      display: table-footer-group;
    }
    button {
      display: none;
    }
    body {
      margin: 0;
    }
  }
  </style>
  `,
  text_politique_1: 'N° du RC 574933 N° du ICE 003230453000072',
  text_politique_2: '2 LOT OTHMANE LOT N 13 AIN CHOCK CASABLANCA - MOROCCO 20600 Casablanca Téléphone : +212660251589',
};