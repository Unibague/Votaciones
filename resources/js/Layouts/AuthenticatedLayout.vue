<template>
    <GeneralLayout>
        <template v-slot:custom-v-app-bar-icon>
            <v-app-bar-nav-icon @click="drawer = true" class="white--text"></v-app-bar-nav-icon>
        </template>
        <template v-slot:app-bar-content>
            <Link as="v-btn" text v-for="menuItem in menu" :key="menuItem.title"
                  v-if="$page.props.user.customRoleId >= menuItem.role "
                  class="d-none d-md-block"
                  :class="{
                        'active-button':route().current(menuItem.routeName),
                        'normal-button':!(route().current(menuItem.routeName))
                        }"
                  :href="route(menuItem.routeName)">
                {{ menuItem.name }}
            </Link>
            <v-menu left bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-avatar
                        v-bind="attrs"
                        v-on="on"
                        color="grey lighten-1"
                        size="32"
                    >
                        <span class="grey--text text--darken-4">{{ initials }}</span>

                    </v-avatar>

                </template>

                <v-list>
                    <v-list-item>
                        <v-list-item-title>
                            {{ $page.props.user.name }}
                        </v-list-item-title>
                    </v-list-item>
                    <v-list-item @click="logout">
                        <v-list-item-title>Cerrar sesion</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </template>
        <template v-slot:custom-navigation-drawer>
            <v-navigation-drawer
                v-model="drawer"
                app
                temporary
                bottom
            >
                <v-list
                    nav
                    dense
                >
                    <v-list-item-group
                        v-model="group"
                        active-class="deep-purple--text text--accent-4"
                    >
                        <Link as="v-list-item" v-for="item in menu" :key="menu.name" :href="route(item.routeName)"
                              v-if="$page.props.user.customRoleId >= item.role">
                            <v-list-item-icon>
                                <v-icon>{{ item.icon }}</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>{{ item.name }}</v-list-item-title>
                        </Link>
                    </v-list-item-group>
                </v-list>

                <template v-slot:append>
                    <p class="text-center">
                        {{ $page.props.user.name }}
                    </p>
                    <div class="pa-2">
                        <v-btn block @click="logout" color="grey darken-4">
                             <span class="grey--text text--lighten-1">
                                Cerrar sesión
                             </span>
                        </v-btn>
                    </div>
                </template>

            </v-navigation-drawer>
        </template>
        <!--This is the main slot, it will contain all application content-->
        <slot></slot>

    </GeneralLayout>
</template>

<script>
import GeneralLayout from "@/Layouts/GeneralLayout";
import {Link} from '@inertiajs/inertia-vue';

export default {
    components: {
        GeneralLayout,
        Link
    },
    data: () => ({
        drawer: false,
        menu:
            [
                {
                    name: 'Gestionar roles',
                    routeName: 'roles.index',
                    role: 3,
                    icon: 'mdi-cog-box'
                },
                {
                    name: 'Gestionar usuarios',
                    routeName: 'users.index',
                    role: 3,
                    icon: 'mdi-account-cog'
                },
                {
                    name: 'Gestionar Dependencias',
                    routeName: 'test',
                    role: 1,
                    icon: 'mdi-school'
                },
                {
                    name: 'Gestionar Calendarios',
                    routeName: 'test',
                    role: 1,
                    icon: 'mdi-calendar'
                },

            ],
        group: null,
        initials: '',
    }),
    methods: {
        logout() {
            this.$inertia.post(route('logout'));
        },
    },

    async created() {
        //Get the inicials
        let name = this.$page.props.user.name;
        let splitName = name.split(' ');
        this.initials = `${splitName[0].charAt(0)}${splitName[1].charAt(0)}`;


    }

}
</script>
<style>

.active-button {
    color: #FAFAFA !important;
}

.normal-button {
    color: #BDBDBD !important;
}

</style>
