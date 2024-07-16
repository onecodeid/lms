// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_reward.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        sdate: window.local_date,
        edate: window.local_date,
        
        redeems: [],
        total_redeem: 0,
        total_redeem_page: 0,
        selected_redeem: {},

        current_page: 1,
        snackbar: false,
        redeem_text: '',

        redeem_type: [{id:'Y',label:'Dikirim'},{id:'X',label:'Diambil'}],
        selected_redeem_type: {id:'Y'},
        dialog_send: false,

        send_date: window.local_date,
        send_note: '',

        redeem_date: window.local_date
    },
    mutations: {
        set_common (state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_redeems(state, data) {
            state.redeems = data
        },

        set_selected_redeem(state, val) {
            state.selected_redeem = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_redeem(state, val) {
            state.total_redeem = val
        },

        update_total_redeem_page(state, val) {
            state.total_redeem_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_sdate(state, val) {
            state.sdate = val
        },

        set_edate(state, val) {
            state.edate = val
        },

        set_selected_redeem_type(state, val) {
            state.selected_redeem_type = val
        }
    },
    actions: {
        async search(context, prm) {
            console.log('sdate')
            console.log(context.state.sdate)
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.search = context.state.search
                prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                let resp = await api.search_redeem(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total,
                        total_page: resp.data.total_page
                    }

                    context.commit("update_redeems", data.records)
                    context.commit("update_total_redeem", data.total)
                    context.commit("update_total_redeem_page", data.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async redeem_cancel(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.redeem_id = context.state.selected_redeem.redeem_id
                prm.note = ""
                let resp = await api.redeem_cancel(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_common', ['redeem_text', resp.message])
                    context.commit('set_common', ['snackbar', true])
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch("search", {})
                    context.dispatch("reward/search_customer", {}, {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async redeem_send(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.redeem_id = context.state.selected_redeem.redeem_id
                prm.redeem_date = context.state.send_date
                prm.redeem_note = context.state.send_note
                prm.redeem_type = context.state.selected_redeem_type.id
                let resp = await api.redeem_send(prm)
                
                context.commit('set_common', ['dialog_send', false])
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_common', ['redeem_text', resp.message])
                    context.commit('set_common', ['snackbar', true])
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch("search", {})
                    // context.dispatch("reward/search_customer", {}, {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        }
    }
}