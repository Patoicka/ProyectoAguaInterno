<script setup>
import BaseButton from "@/Components/BaseButton.vue";
import BaseButtons from "@/Components/BaseButtons.vue";
import NavLink from "@/Components/NavLink.vue";
import { Button, Toggle } from "flowbite-vue";
import { Link, usePage } from '@inertiajs/vue3';
import { useStyleStore } from "@/stores/style.js";
import { ref } from 'vue';
import { mdiClose, mdiMenu, mdiThemeLightDark } from '@mdi/js';
import BaseIcon from "@/Components/BaseIcon.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { appName } from "@/Hooks/useAppName";

const styleStore = useStyleStore();
const isDarkMode = ref(styleStore.darkMode);
const isAuthenticate = usePage().props.auth.user?.id ? true : false;

const handleToggle = () => {
    styleStore.setDarkMode();
};

const showMenu = ref(false);
const toggleNav = () => {
    showMenu.value = !showMenu.value;
}

</script>

<template>
    <div class="bg-white dark:bg-blue-950">
        <nav class="container px-6 py-8 mx-auto lg:flex lg:justify-start lg:items-center">
            <div class="flex items-center justify-between lg:w-72 w-auto">
                <Link href="/"
                    class="text-xl font-bold text-blue-600 dark:text-gray-100 md:text-3xl hover:text-blue-500">
                <div class="justify-normal flex items-center space-x-1">
                    <ApplicationLogo inputClass="w-14 md:rounded-lg rounded-b-full rounded-t-lg" />
                    <div class="text-[#737373] dark:text-white">
                        <p>{{ appName() }}</p>
                        <p class="text-xs">Comisión Nacional del Agua</p>
                    </div>
                </div>
                </Link>

                <!-- Mobile menu button -->
                <div @click="toggleNav" class="flex lg:hidden">
                    <BaseButton :icon="showMenu ? mdiClose : mdiMenu" color="info" class="text-white" />
                </div>
            </div>

            <div :class="showMenu ? 'flex flex-col' : 'hidden'"
                class="pb-4 lg:pb-0 lg:flex lg:flex-row lg:justify-between lg:w-full">
                <ul
                    class="w-full order-2 flex-col mt-4 space-y-2 lg:ml-20 lg:order-1 lg:flex lg:space-y-0 lg:flex-row lg:items-center lg:space-x-10 lg:mt-0">
                    <li>
                        <NavLink :active="$page.component === 'Welcome/Home/Index'" href="/">Inicio</NavLink>
                    </li>
                    <li>
                        <NavLink :active="$page.component === 'Welcome/Incident/Create'" href="/welcome/incident/create">
                            Incidencias
                        </NavLink>
                    </li>
                    <li>
                        <NavLink :active="$page.component === 'Welcome/Incident/Search'" href="/welcome/incident/search">
                            Seguimiento de incidencias
                        </NavLink>
                    </li>
                    <!-- <li>
                        <NavLink href="#">Programa técnico</NavLink>
                    </li> -->
                    <!-- <li>
                        <NavLink :active="$page.component === 'Welcome/Place/Index'" href="/lugar">Lugar</NavLink>
                    </li>
                    <li>
                        <NavLink :active="$page.component === 'Welcome/Program/Index'" href="/programa">Programa
                        </NavLink>
                    </li> -->
                </ul>

                <div class="order-1 flex justify-center items-center mt-4 lg:order-2 lg:mt-0 lg:flex lg:items-center">

                    <label class="inline-flex items-center cursor-pointer mr-4 gap-1">
                        <BaseIcon :path="mdiThemeLightDark" class="dark:text-white" />
                        <!-- Interruptor -->
                        <input @click="handleToggle" type="checkbox" v-model="isDarkMode" class="sr-only peer">
                        <div
                            class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
                        </div>
                    </label>

                    <div class="flex gap-4">
                        <BaseButton v-if="!isAuthenticate" outline small label="Iniciar sesión" color="info"
                            routeName="login" />
                        <!-- <BaseButton v-if="!isAuthenticate" small label="Registrarse" color="info"
                            routeName="register" /> -->
                        <BaseButton v-if="isAuthenticate" small label="Inicio" color="info" routeName="dashboard" />
                    </div>
                </div>
            </div>
        </nav>
    </div>
</template>