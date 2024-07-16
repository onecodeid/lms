// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_omzet_province.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        omzet_provinces: [],
        total_omzet_province: 0,
        // total_omzet_province_page: 0,
        selected_omzet_province: {},

        omzet_customers: [],
        selected_omzet_customer: {},

        paretos: [],

        types: [{text:'ITEM & PAKET',value:'A'},{text:'ITEM',value:'I'},{text:'PAKET',value:'P'}],
        selected_type: {text:'ITEM & PAKET',value:'A'},

        users: [],
        selected_user: null,

        omzet_high: 100,
        customer_high: 100,
        omzet_length: 0,

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

        update_omzet_provinces(state, val) {
            state.omzet_high = 100
            if (val.length > 0)
                state.omzet_high = val[0].omzet
            
            for (let i in val)
                val[i].scale = Math.round((val[i].omzet / state.omzet_high) * 100)
            state.omzet_provinces = val
            state.omzet_length = val.length
        },

        update_omzet_customers(state, val) {
            state.customer_high = 100
            if (val.length > 0)
                state.customer_high = val[0].omzet
            
            for (let i in val)
                val[i].scale = Math.round((val[i].omzet / state.customer_high) * 100)
            state.omzet_customers = val
        },

        set_selected_omzet_province(state, val) {
            state.selected_omzet_province = val
        },

        set_selected_omzet_customer(state, val) {
            state.selected_omzet_customer = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_omzet_province(state, val) {
            state.total_omzet_province = val
        },

        // update_total_omzet_province_page(state, val) {
        //     state.total_omzet_province_page = val
        // },

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

        set_selected_level (state, val) {
            state.selected_level = val
        },

        set_paretos (state, val) {
            state.paretos = val
        }
    },
    actions: {
        async search(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.search = context.state.search
                // prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.type = context.state.selected_type.value
                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_omzet_provinces", resp.data[0])
                    // context.commit("update_omzet_customers", resp.data[1])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_pareto(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                let cities = []
                for (let c of context.state.selected_omzet_province.omzet_cities)
                    cities.push(c.city_id)
                prm.token = one_token()
                prm.cities = cities
                // prm.page = context.state.current_page
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.type = context.state.selected_type.value
                let resp = await api.search_pareto(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_paretos", resp.data)
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
        //         prm.id = context.state.selected_omzet_province.M_ExpenseID

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