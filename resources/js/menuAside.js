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
    mdiEye,
    mdiCity,
    mdiTag,
    mdiCheckCircle,
    mdiCalendar,
    mdiTelevisionGuide,
    mdiMapMarkerAlertOutline,
    mdiChartBarStacked,
    mdiTableCog
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

    {
        label: "Visualización",
        icon: mdiTelevisionGuide,
        menu: [
            {
                label: "Mapa de incidencias",
                route: "incident/Map",
                icon: mdiMapMarkerAlertOutline,
            },
            {
                label: "Gráfico de incidencias",
                route: "graphic.index",
                icon: mdiChartBarStacked,
            },
            {
                label: "Ver Incidencias",
                route: "incidents.view",
                icon: mdiFire,
            },
            {
                label: "Por Municipio",
                route: "incidents.byMunicipality",
                icon: mdiCity,
            },
            {
                label: "Por Tipo",
                route: "incidents.byType",
                icon: mdiTag,
            },
            {
                label: "Por Estatus",
                route: "incidents.byStatus",
                icon: mdiCheckCircle,
            },
            {
                label: "Por Fecha",
                route: "incidents.byDate",
                icon: mdiCalendar,
            },
            {
                label: "Consulta dinámica",
                route: "dynamic.search",   // nombre de ruta que ya existe
                icon: mdiTableCog
            }
        ],
    },
];
