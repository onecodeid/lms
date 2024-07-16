// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_setting.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: true,
        search_status: 0,
        search_error_message: '',
        search: '',
        setting: {},
        invoice_number: null,

        tabs: [{ id: 1, label: 'Watzap Settings' }],
        selected_tab: null
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

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_setting(state, v) { state.setting = v },
        set_selected_tab(state, v) { state.selected_tab = v }
    },
    actions: {
        async get_conf(context) {
            context.dispatch("system/postme", {
                url: "crm/watzapid/get_conf",
                prm: {},
                callback: function(d) {
                    context.commit("set_object", ["setting", d])

                    if (!!d.invoice_number)
                        context.commit("set_object", ["invoice_number", d.invoice_number])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })

        },

        async save_conf(context) {
            let invoice_number = null
            if (!!context.state.invoice_number)
                invoice_number = JSON.stringify({ key: context.state.invoice_number.key, wa_number: context.state.invoice_number.wa_number })
            context.dispatch("system/postme", {
                url: "crm/watzapid/save_conf",
                prm: { id: context.state.setting.id, invoice_number: invoice_number },
                callback: function(d) {
                    // context.commit("set_object", ["setting", d])

                    // if (!!d.invoice_number)
                    //     context.commit("set_object", ["invoice_number", d.invoice_number])
                    alert("sukses !")
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })

        },

        async refresh(context) {
            context.dispatch("system/postme", {
                url: "crm/watzapid/status",
                prm: { id: context.state.setting.id },
                callback: function(d) {

                    if (!!context.state.invoice_number) {
                        let key_invoice = null
                        for (let k of d.data.licenses_key) {
                            if (k.key == context.state.invoice_number.key)
                                key_invoice = k;
                        }
                        context.commit("set_object", ["invoice_number", key_invoice]);
                    }

                    // context.commit("set_object", ["setting", d])

                    // if (!!d.invoice_number)
                    //     context.commit("set_object", ["invoice_number", d.invoice_number])
                    context.dispatch("get_conf")
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })

        }
    }
}