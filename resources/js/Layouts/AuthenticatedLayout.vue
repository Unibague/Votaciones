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

            <v-menu
                v-for="dropdown in dropdowns" :key="dropdown.title"
                v-if="$page.props.user.customRoleId >= dropdown.role"
                bottom
                origin="center center"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-btn text
                           v-bind="attrs"
                           v-on="on"
                           class="d-none d-md-block normal-button mr-3">
                        {{ dropdown.name }}
                    </v-btn>
                </template>

                <v-list>
                    <Link as="v-list-item"
                          :href="route(item.routeName)"
                          v-for="item in dropdown.items"
                          :key="item.name"
                    >
                        <v-list-item-title>{{ item.name }}</v-list-item-title>
                    </Link>
                </v-list>
            </v-menu>


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

                        <v-list-group
                            v-for="dropdown in dropdowns"
                            :key="dropdown.title"
                            v-model="dropdown.active"
                            :prepend-icon="dropdown.icon"
                            no-action
                        >
                            <!-- activator -->
                            <template v-slot:activator>
                                <v-list-item-content>
                                    <v-list-item-title v-text="dropdown.name"></v-list-item-title>
                                </v-list-item-content>
                            </template>

                            <Link as="v-list-item"
                                  v-for="dropdownItem in dropdown.items"
                                  :key="dropdownItem.name"
                                  :href="route(dropdownItem.routeName)"
                            >
                                <v-list-item-content>
                                    <v-list-item-title v-text="dropdownItem.name"></v-list-item-title>
                                </v-list-item-content>
                            </Link>


                        </v-list-group>


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
            [ {
                name: 'Habilitar voto',
                routeName: 'votes.authorize',
                role: 2,
                icon: 'mdi-calendar'
            },],
        dropdowns: [
            {
                name: 'Gestionar elecciones',
                role: 3,
                active: false,
                icon: 'mdi-cog-box',
                items: [
                    {
                        name: 'Opciones de votación',
                        routeName: 'votingOptions.index',
                        role: 3,
                        icon: 'mdi-calendar'
                    },

                    {
                        name: 'Candidatos',
                        routeName: 'candidates.index',
                        role: 3,
                        icon: 'mdi-calendar'
                    },
                    {
                        name: 'Jurados',
                        routeName: 'jurors.index',
                        role: 3,
                        icon: 'mdi-account-cog'
                    },
                    {
                        name: 'Mesas de votación',
                        routeName: 'tables.index',
                        role: 3,
                        icon: 'mdi-calendar'
                    },

                    {
                        name: 'Resultados generales',
                        routeName: 'tables.index',
                        role: 3,
                        icon: 'mdi-calendar'
                    },

                ]

            },
            {
                name: 'Gestionar sistema',
                role: 3,
                active: false,
                icon: 'mdi-cog-box',
                items: [
                    {
                        name: 'Roles',
                        routeName: 'roles.index',
                        role: 3,
                        icon: 'mdi-cog-box'
                    },
                    {
                        name: 'Usuarios',
                        routeName: 'users.index',
                        role: 3,
                        icon: 'mdi-account-cog'
                    },

                    {
                        name: 'Facultades',
                        routeName: 'faculties.index',
                        role: 3,
                        icon: 'mdi-cog-box'
                    },
                    {
                        name: 'Programas',
                        routeName: 'programs.index',
                        role: 3,
                        icon: 'mdi-calendar'
                    },


                ]

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
