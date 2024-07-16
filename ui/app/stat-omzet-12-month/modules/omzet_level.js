// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_omzet_level.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        omzet_levels: [],
        total_omzet_level: 0,
        // total_omzet_level_page: 0,
        selected_omzet_level: {},

        omzet_customers: [],
        selected_omzet_customer: {},

        types: [{text:'ITEM & PAKET',value:'A'},{text:'ITEM',value:'I'},{text:'PAKET',value:'P'}],
        selected_type: {text:'ITEM & PAKET',value:'A'},

        users: [],
        selected_user: null,

        levels: [],
        selected_levelx: null,

        omzet_min: 0,
        omzet_max: 0,

        omzet_high: 100,
        customer_high: 100,

        current_page: 1,
        sdate: moment().subtract(30, 'days').format('YYYY-MM-DD'),
        edate: new Date().toISOString().substr(0, 10),
        dtx: false,

        selected_level: null
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

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_omzet_levels(state, val) {
            state.omzet_high = 100
            if (val.length > 0)
                state.omzet_high = val[0].omzet
            
            for (let i in val)
                val[i].scale = Math.round((val[i].omzet / state.omzet_high) * 100)
            state.omzet_levels = val
        },

        update_omzet_customers(state, val) {
            state.customer_high = 100
            if (val.length > 0)
                state.customer_high = val[0].omzet
            
            for (let i in val)
                val[i].scale = Math.round((val[i].omzet / state.customer_high) * 100)
            state.omzet_customers = val
        },

        set_selected_omzet_level(state, val) {
            state.selected_omzet_level = val
        },

        set_selected_omzet_customer(state, val) {
            state.selected_omzet_customer = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_omzet_level(state, val) {
            state.total_omzet_level = val
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

        set_selected_type (state, val) {
            state.selected_type = val
        },

        set_levels (state, val) {
            state.levels = val
        },

        set_selected_level (state, val) {
            state.selected_level = val
        },

        set_selected_levelx (state, val) {
            state.selected_levelx = val
        },

        set_users(state, val) {
            state.users = val
        },

        set_selected_user(state, val) {
            state.selected_user = val
        }
    },
    actions: {
        async search(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                let level = []
                prm.token = one_token()
                prm.search = context.state.search
                // prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.type = context.state.selected_type.value
                prm.omzet_min = context.state.omzet_min
                prm.omzet_max = context.state.omzet_max
                prm.user = context.state.selected_user ? context.state.selected_user.user_id : 0

                for (let l of context.state.selected_levelx)
                    level.push(l.M_CustomerLevelID)
                prm.level = JSON.stringify(level)
                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_omzet_levels", resp.data[0])
                    context.commit("update_omzet_customers", resp.data[1])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_qty(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.search = context.state.search
                // prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.type = context.state.selected_type.value
                let resp = await api.search_qty(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_qty_products", resp.data)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_level(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token : one_token() }
                let resp = await api.search_level(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_levels", resp.data.records)
                    context.commit('set_selected_levelx', resp.data.records)
                    context.dispatch('search')
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
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
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        }
    }
}