// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_system.js?t=123"
import * as api2 from "./api_common.min.js?t=123"
import { URL, one_token, one_user } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search: '',
        search_status: 0,
        search_error_message: "",
        menu_level_0: [],
        menu_level_1: [],
        menu_level_2: [],
        total_menu: 0,
        //sipe add breadcrumb dan hak akses
        bread_crumb: "",
        is_page_allowed: true,
        dashboard: "",
        user: one_user(),
        one_token: one_token(),

        menus: [],
        icons: [],
        drawer: false,

        notif_unread: 0,
        notif_total: 0,
        notif_messages: [],
        notif_id: 0,

        popup_unread: 0,
        popup_total: 0,
        popup_messages: [],
        popup_md5: '',

        drawer_notif: false,
        wscon: null,
        notif_md5: '',
        audio: null,
        sound: 'https://soundbible.com/mp3/Elevator Ding-SoundBible.com-685385892.mp3',
        notif_muted: true,
        dialog_notif: false,

        wa_url: 'https://api.whatsapp.com/send/',
        URL: URL,

        // notices
        notices: [],
        selected_notice: null,
        popup_notices: [],
        dialog_notice: false,
        notice_id: 0
    },

    mutations: {
        set_common(state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        set_object(state, v) {
            let name = v[0]
            let val = v[1]
            state[name] = val
        },

        update_bread_crumb(state, val) {
            state.bread_crumb = val
        },
        update_dashboard(state, val) {
            state.dashboard = val
        },
        update_page_allowed(state, val) {
            state.is_page_allowed = val
        },
        update_search(state, val) {
            state.search = val
        },
        update_search_error_message(state, status) {
            state.search_error_message = status
        },
        update_search_status(state, status) {
            state.search_status = status
        },

        update_menu_level_0(state, data) {
            state.menu_level_0 = data
        },

        update_menu_level_1(state, data) {
            state.menu_level_1 = data
        },

        update_menu_level_2(state, data) {
            state.menu_level_2 = data
        },

        set_menus(state, v) {
            state.menus = v
        },

        set_icons(state, v) {
            state.icons = v
        },

        set_drawer(state, v) {
            state.drawer = v
        },

        set_drawer_notif(state, v) {
            state.drawer_notif = v
        },

        set_notif_unread(state, v) {
            state.notif_unread = v
        },

        set_notif_total(state, v) {
            state.notif_total = v
        },

        set_notif_messages(state, v) {
            state.notif_messages = v
        },

        set_notif_md5(state, v) {
            state.notif_md5 = v
        },

        set_wscon(state, v) {
            state.wscon = v
        },

        set_audio(state, v) {
            state.audio = v
        },

        set_notif_muted(state, v) {
            state.notif_muted = v
        }
    },
    actions: {
        async search_menu_group(context) {
            context.commit("update_search_status", 1)
            try {
                let resp = await api.search_menu_group({ token: one_token() })
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('set_menus', resp.data[0])
                    context.commit('set_icons', resp.data[1])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async logout() {
            try {
                var resp = await axios.post(URL + 'systm/user/logout', { token: one_token() });
                if (resp.status != 200) {
                    return {
                        status: "ERR",
                        message: resp.statusText
                    };
                }
                let data = resp.data;
                return data;
            } catch (e) {
                return {
                    status: "ERR",
                    message: e.message
                };
            }
        },

        async do_logout(context) {
            let x = async() => {
                try {
                    let resp = await context.dispatch('logout')
                    if (resp.status != "OK") {
                        alert('error')
                    } else {
                        window.localStorage.removeItem("token")
                        window.localStorage.removeItem("user")
                        location.replace('../system-login/')
                    }
                } catch (e) {
                    context.commit("update_login_status", 3)
                    context.commit("update_login_error_message", e.message)
                }
            }

            x()
        },

        async get_notif_unread(context) {
            context.commit("update_search_status", 1)
            try {
                let resp = await api.get_notif_unread({ token: one_token() })
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    if (resp.data.md5 != context.state.notif_md5 || resp.data.popups.md5 != context.state.popup_md5) {
                        context.commit('set_notif_unread', resp.data.unread)
                        context.commit('set_notif_total', resp.data.total)
                        context.commit('set_notif_messages', resp.data.messages)
                        context.commit('set_notif_md5', resp.data.md5)

                        context.commit('set_object', ['popup_unread', resp.data.popups.unread])
                        context.commit('set_object', ['popup_total', resp.data.popups.total])
                        context.commit('set_object', ['popup_messages', resp.data.popups.messages])
                        context.commit('set_object', ['popup_md5', resp.data.popups.md5])

                        console.log(resp.data.popups.messages)
                        console.log(resp.data.popups.messages.length)
                        context.commit('set_object', ['dialog_notif', (resp.data.popups.messages.length > 0 ? true : false)])

                        // let audio = context.state.audio
                        // if (audio)
                        //     audio.play();
                    }
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async set_notif_read(context) {
            context.commit("update_search_status", 1)
            try {
                let resp = await api.set_notif_read({ token: one_token() })
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('get_notif_unread')
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async set_notif_read_id(context) {
            context.commit("update_search_status", 1)
            try {
                let resp = await api.set_notif_read_id({ token: one_token(), id: context.state.notif_id })
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('get_notif_unread')

                    return resp.data
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async report_excel(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.report_excel(prm.url, prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                        // console.log(resp.data.report_url)
                    window.open(resp.data.report_url)
                    context.commit("set_dialog_print", false, { root: true })
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async postme(context, d) {
            let aurl = d.url
            let prm = d.prm
            let hdr = d.hdr
            let callback = d.callback
            let failback = d.failback

            context.commit("set_common", ['search_status', 1])
            try {
                prm.token = context.state.one_token
                let resp = await api2.postme(aurl, prm, hdr)

                if (resp.status != "OK") {
                    context.dispatch('postme_fail', resp)
                    if (failback)
                        failback(resp.message)

                    return resp
                } else {
                    context.dispatch('postme_success')
                    if (callback)
                        callback(resp.data)

                    // if (prm.return)
                        return resp.data

                }
            } catch (e) {
                context.dispatch('postme_fail', e)
                console.log(e)
                if (failback)
                    failback(e.message)
            }
        },

        async get_notice_unread(context) {
            let prm = {
                token: one_token(),
                limit: 999,
                search: ''
            }

            context.dispatch("postme", {
                url: "notice/message/get_unread",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['dialog_notice', (d.length > 0 ? true : false)])
                    context.commit('set_object', ['notices', d])
                    context.commit('set_object', ['popup_notices', d])
                }
            })
        },

        async set_notice_read(context) {
            let prm = {
                token: one_token(),
                notice_id: context.state.selected_notice.message_id,
                return: true
            }

            return context.dispatch("postme", {
                url: "notice/message/set_read",
                prm: prm,
                callback: function(d) {
                    return d
                }
            })
        },

        async set_notice_softread(context) {
            let prm = {
                token: one_token(),
                notice_id: context.state.selected_notice.message_id,
                return: true
            }

            return context.dispatch("postme", {
                url: "notice/message/set_softread",
                prm: prm,
                callback: function(d) {
                    return d
                }
            })
        },

        postme_success(context) {
            context.commit("set_common", ['search_status', 2])
            context.commit("set_common", ['search_error_message', ""])
        },

        postme_fail(context, e) {
            context.commit("set_common", ['search_status', 3])
            context.commit("set_common", ['search_error_message', e.message])
        }
    }
}