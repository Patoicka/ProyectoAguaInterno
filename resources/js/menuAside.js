import {
  mdiAccountCircle,
  mdiMonitor,
  mdiBullhorn,
  mdiDomain,
  mdiShieldCrown,
  mdiViewList,
  mdiViewModule,
  mdiLockCheckOutline,
  mdiAccountSupervisor,
  mdiAccount,
  mdiBook,
  mdiShieldLock,
  mdiFileDocumentCheck,
  mdiAccountGroup,
  mdiFire,
  mdiMapMarkerAlertOutline,
  mdiTelevisionGuide,
  mdiFileDocument,
  mdiChartBarStacked,
} from "@mdi/js";
// import Icon from '@mdi/react';

export default [
  {
    route: "dashboard",
    icon: mdiMonitor,
    label: "Inicio",
    to: "/dashboard",
  },
  {
    route: "profile.edit",
    label: "Perfil",
    icon: mdiAccountCircle,
  },

  {
    label: "Seguridad",
    icon: mdiShieldLock,
    role: "Admin",
    permission: "menu.seguridad",
    menu: [
      {
        label: "Modulos",
        route: "module.index",
        icon: mdiViewModule,
        permission: "module.index",
      },
      {
        label: "Permisos",
        route: "permission.index",
        icon: mdiLockCheckOutline,
        permission: "permission.index",
      },
      {
        label: "Roles",
        route: "role.index",
        icon: mdiAccountSupervisor,
        permission: "role.index",
      },
      {
        label: "Usuarios",
        route: "user.index",
        icon: mdiAccount,
        permission: "user.index",
      },
    ],
  },
  {
    label: "Visualización",
    icon: mdiTelevisionGuide,
    menu: [
      {
        label: "Mapa de incidencias",
        route: "incident/Map",
        icon: mdiMapMarkerAlertOutline,
      },
    ],
  },
  {
    isDivider: true,
  },
  {
    label: "Tipos de incidencias",
    route: "incidentType.index",
    icon: mdiViewList,
    permission: "incidentType.index",
  },
  {
    label: "Incidencias",
    route: "incident.index",
    icon: mdiFire,
    permission: "incident.index",
  },
];
