<template>
    <header>
        <div id="header-inner">
            <div id="logo"></div>
            <div id="user-controls">
                <NestedDropdown id="lang-setting">
                    <template #parent>
                        <div id="current-lang">
                            <img alt="globe_wireframe_32" src="/images/icons/globe_wireframe_32.svg"/>
                            <span>{{ __('frontend.header.lang.' + language.slug) }}</span>
                        </div>
                    </template>
                    <template #dropdown>
                        <div id="lang-list">
                            <ul>
                                <li v-for="lang in langList"
                                    :key="lang.slug"
                                    :class="currentLangClass(lang.slug)"
                                    @click="setCurrentLang(lang.slug)">
                                    {{ __('frontend.header.lang.' + lang.slug) }}
                                </li>
                            </ul>
                        </div>
                    </template>
                </NestedDropdown>
                <NestedDropdown id="user-account">
                    <template #parent>
                        <div id="user-icons">
                            <img alt="user_avatar_32" src="/images/icons/user_avatar_32.svg" />
                            <img alt="arrow_simple_32" src="/images/icons/arrow_simple_32.svg" class="arrow" />
                        </div>
                    </template>
                    <template #dropdown>
                        <div id="user-profile">
                            test
                        </div>
                    </template>
                </NestedDropdown>
            </div>
        </div>
    </header>
</template>

<script>
import {mapActions, mapState} from 'vuex';
import NestedDropdown from "../General/NestedDropdown";

export default {
    name: "BaseHeader",

    components: {NestedDropdown},

    computed: mapState('config', {
        language: state => state.language,
        langList: state => state.langList,
    }),

    methods: {
        ...mapActions('config', [
            'setCurrentLang',
        ]),

        currentLangClass(key) {
            return this.language.slug === key ? 'selected' : '';
        }
    },

}
</script>

<style lang="scss" scoped>
@import 'resources/css/fonts';
@import 'resources/css/variables';

header {
    z-index: 10;
    width: 100%;
    height: 48px;
    display: flex;
    position: fixed;
    color: $gray-main;
    flex-direction: row;
    justify-content: center;
    background-color: $black-main;
    font-family: "Open Sans", sans-serif;

    #header-inner {
        display: flex;
        min-width: 960px;
        flex-direction: row;
        justify-content: space-between;

        #logo {
            width: 96px;
            height: 96px;
            user-select: none;
            background: center / contain no-repeat url("/images/logos/rcs_128.svg");
        }

        #user-controls {
            height: 48px;
            display: flex;
            font-size: 14px;
            flex-direction: row;

            #lang-setting {
                display: flex;
                font-weight: bold;
                user-select: none;
                position: relative;
                flex-direction: column;

                #current-lang {
                    height: 48px;
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    text-decoration: underline;

                    * {
                        margin: 0 4px;
                    }

                    img {
                        width: 24px;
                        height: 24px;
                    }

                    span {
                        margin-left: 0;
                    }

                    &:hover {
                        cursor: pointer;
                        background-color: adjust-color($black-main, $lightness: 10%);
                    }
                }

                #lang-list {
                    width: 100%;
                    z-index: 20;
                    position: absolute;
                    border-radius: 0 0 8px 8px;
                    background-color: $black-main;

                    ul {
                        padding: 0;
                        display: flex;
                        align-items: center;
                        flex-direction: column;

                        li {
                            width: 100%;
                            text-align: center;
                            list-style-type: none;

                            &:hover {
                                cursor: pointer;
                                background-color: adjust-color($black-main, $lightness: 10%);
                            }

                            &.selected {
                                text-decoration: underline;
                            }
                        }
                    }
                }
            }

            #user-account {
                display: flex;
                position: relative;
                flex-direction: column;

                #user-icons {
                    height: 48px;
                    display: flex;
                    user-select: none;
                    flex-direction: row;
                    align-items: center;

                    * {
                        margin: 0 4px;
                    }

                    &:hover {
                        cursor: pointer;
                        background-color: adjust-color($black-main, $lightness: 10%);
                    }

                    .arrow {
                        width: 8px;
                        height: 8px;
                        margin-left: 0;
                    }

                }

                #user-profile {
                    right: 0;
                    z-index: 20;
                    width: 256px;
                    display: flex;
                    padding: 16px;
                    min-height: 128px;
                    position: absolute;
                    border-radius: 0 0 8px 8px;
                    background-color: $black-main;

                }
            }
        }
    }
}
</style>
