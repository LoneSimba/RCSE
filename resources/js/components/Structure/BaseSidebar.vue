<template>
    <div id="main-sidebar">
        <nav>
            <ul id="main-menu">
               <li v-for="item in mainMenu"
                   :key="item.slug"
                   class="menu-item">
                   <a :href="item.url">
                       <span class="menu-item-text">{{ __('frontend.main_menu.' + item.slug) }}</span>
                       <span class="menu-item-icon"
                             :class="item.slug">
                           <img :alt="item.icon" :src="'/images/icons/' + item.icon + '.svg'">
                       </span>
                   </a>
               </li>
            </ul>
        </nav>
    </div>
</template>

<script>
import {mapActions, mapState} from "vuex";

export default {
    name: "BaseSidebar",

    computed: mapState('config', {
        mainMenu: state => state.mainMenu,
        language: state => state.language
    }),

    methods: mapActions('config', [
        'getMainMenu'
    ]),

    beforeCreate() {
        this.$store.dispatch('config/getMainMenu')
    }
}
</script>

<style lang="scss" scoped>
@import 'resources/css/fonts';
@import 'resources/css/variables';

#main-sidebar {
    width: 64px;
    display: flex;
    font-size: 14pt;
    font-weight: bold;
    color: $gray-main;
    padding-top: 48px;
    align-items: center;
    flex-direction: column;
    background-color: $black-main;
    font-family: "Open Sans", sans-serif;

    #main-menu {
        padding: 0;
        margin: 0;

        li {
            height: 40px;
            display: block;
            position: relative;
            list-style-type: none;

            a {
                height: inherit;
                color: $gray-main;
                text-decoration: none;

                .menu-item-text {
                    width: 0;
                    right: 100%;
                    height: inherit;
                    overflow: hidden;
                    position: absolute;
                    align-items: center;
                    display: inline-flex;
                    transform-origin: right;
                    // text-transform: uppercase;
                    border-radius: 4px 0 0 4px;
                    background-color: $black-main;
                    transition: all .15s ease-in-out;
                }

                .menu-item-icon {
                    width: 64px;
                    height: inherit;
                    align-items: center;
                    display: inline-flex;
                    justify-content: center;
                }
            }

            &:hover {
                .menu-item-text {
                    width: 175%;
                    padding: 0 8px;
                }
            }
        }
    }
}
</style>
