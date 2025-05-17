<script setup>
import HeadLogo from "@/Components/HeadLogo.vue";
import LayoutWelcome from "@/Layouts/LayoutWelcome.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import BaseButtons from "@/Components/BaseButtons.vue";
import BaseButton from "@/Components/BaseButton.vue";
import FormField from "@/Components/FormField.vue";
import FormControl from "@/Components/FormControl.vue";
import { mdiArrowRight, mdiQrcode } from "@mdi/js";
import BaseDivider from "@/Components/BaseDivider.vue";
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const props = defineProps({
  title: { type: String, required: true },
  code: { type: String, required: false, default: null },
});

const form = useForm({ code: props.code });

const validateAndSubmit = () => {
  if (!form.code || form.code.trim() === "") {
    Swal.fire({
      icon: "warning",
      title: "Tecleé un Número de Folio",
      text: "Por favor, ingrese un número de folio antes de continuar.",
      confirmButtonText: "Aceptar",
    });
    return;
  }

  // Enviar formulario si el código es válido
  form.get(route("welcome.show.incident", { code: form.code }));
};
</script>

<template>
  <HeadLogo :title="title" />
  <LayoutWelcome>
    <section class="min-h-screen py-20 bg-white dark:bg-blue-950">
      <div
        class="max-w-screen-xl mx-auto text-gray-600 gap-x-12 items-center justify-between overflow-hidden md:flex md:px-8"
      >
        <div class="flex-none space-y-5 px-4 sm:max-w-lg md:px-0 lg:max-w-xl">
          <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">
            NUEVO
          </p>
          <h1
            class="text-4xl text-gray-800 dark:text-blue-400 font-extrabold md:text-5xl"
          >
            Buscador de incidencias
          </h1>
          <form class="space-y-5" @submit.prevent="validateAndSubmit">
            <p class="dark:text-gray-400">
              <FormField
                label="Código"
                :error="form.errors.code"
                help="Ingresa el número de folio"
              >
                <FormControl
                  v-model="form.code"
                  placeholder="Ingresa el número de folio"
                  :icon="mdiQrcode"
                />
              </FormField>
            </p>

            <BaseButtons>
              <BaseButton
                :class="[{ 'animate-bounce animate-twice': form.code != null }]"
                label="Consultar"
                type="submit"
                color="info"
                :icon="mdiArrowRight"
              />
            </BaseButtons>
          </form>
        </div>
        <div class="flex-none mt-14 md:mt-0 hidden md:block">
          <img
            src="/img/qr.gif"
            class="lg:w-96 md:w-60 dark:bg-white rounded-3xl"
            alt=""
            width="380"
          />
        </div>
        <div class="flex-none w-full block md:hidden">
          <BaseDivider />
          <div
            class="p-4 text-center flex items-center justify-center h-full text-white lg:bg-black/45"
          >
            <div class="block px-2">
              <h2
                class="font-bold text-xl lg:text-3xl mb-4 text-black lg:text-white dark:text-white"
              >
                ¿Quieres registrar una nueva incidencia?
              </h2>
              <p class="text-xs text-gray-600 lg:text-white dark:text-white">
                Hazlo de forma rapida y sencilla
              </p>
              <Link
                :href="route('welcome.store.incidet')"
                class="lg:hidden text-xs text-blue-500 underline-offset-4 hover:underline hover:text-blue-700 hover:dark:text-blue-400"
              >
                Saber más
              </Link>

              <BaseButton
                class="w-auto mt-6 hidden lg:flex"
                type="submit"
                color="white"
                label="Volver al inicio"
                routeName="welcome"
                small
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  </LayoutWelcome>
</template>
