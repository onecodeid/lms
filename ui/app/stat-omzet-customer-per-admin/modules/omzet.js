// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_omzet.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        fees: [],
        total_fee: 0,
        total_fee_page: 1,
        selected_fee: {},

        omzets: [],
        selected_omzet: null,

        users: [],
        selected_user: null,
        total_omzet: 0,
        total_omzet_page: 1,

        current_page: 1,
        sdate: new Date().toISOString().substr(0, 10),
        edate: new Date().toISOString().substr(0, 10),
        dtx: false
    },
    mutations: {
        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_fees(state, data) {
            state.fees = data
        },

        set_selected_fee(state, val) {
            state.selected_fee = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_fee(state, val) {
            state.total_fee = val
        },

        update_total_fee_page(state, val) {
            state.total_fee_page = val
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

        set_dtx(state, val) {
            state.dtx = val
        },

        set_users(state, val) {
            state.users = val
        },

        set_selected_user(state, val) {
            state.selected_user = val
        },

        set_omzets(state, val) {
            state.omzets = val
        },

        set_selected_omzet(state, val) {
            state.selected_omzet = val
        },

        set_total_omzet(state, val) {
            state.total_omzet = val
        },

        set_total_omzet_page(state, val) {
            state.total_omzet_page = val
        }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.search = context.state.search
                prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                if (context.state.selected_user)
                    prm.uid = context.state.selected_user.user_id
                let resp = await api.search(prm)
                
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

                    context.commit("update_fees", data.records)
                    context.commit("update_total_fee", data.total)
                    context.commit("update_total_fee_page", data.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search2(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.search = context.state.search
                prm.page = context.state.current_page
                prm.order_by = "CURR_MONTH_ASC"
                if (context.state.selected_user)
                    prm.user_id = context.state.selected_user.user_id
                let resp = await api.search2(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    // let data = {
                    //     records: resp.data.records,
                    //     total: resp.data.total,
                    //     total_page: resp.data.total_page
                    // }

                    context.commit("set_omzets", resp.data)
                    context.commit("set_total_omzet", resp.total)
                    context.commit("set_total_omzet_page", resp.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_user(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.search = ""
                prm.group_id = 2
                let resp = await api.search_user(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_users", resp.data.records)
                    context.dispatch("search", {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        }
        // async del(context, prm) {
        //     context.commit("update_search_status", 1)
        //     try {
        //         prm.token = one_token()
        //         prm.id = context.state.selected_fee.M_ExpenseID

        //         let resp = await api.del(prm)
        //         if (resp.status != "OK") {
        //             context.commit("update_search_status", 3)
        //             context.commit("update_search_error_message", resp.message)
        //         } else {
        //             context.commit("update_search_status", 2)
        //             context.commit("update_search_error_message", "")
                    
        //             context.dispatch('search', {})
        //         }
        //     } catch (e) {
        //         context.commit("update_search_status", 3)
        //         context.commit("update_search_error_message", e.message)
        //         console.log(e)
        //     }
        // }
    }
}