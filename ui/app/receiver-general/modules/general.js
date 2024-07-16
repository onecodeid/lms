// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_general.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        generals: [],
        total_general: 0,
        total_general_page: 0,
        selected_general: {},

        search_province_status: 0,
        provinces: [],
        selected_province: null,

        search_city_status: 0,
        cities: [],
        selected_city: null,

        current_page: 1
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

        update_generals(state, data) {
            state.generals = data
        },

        set_selected_general(state, val) {
            state.selected_general = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_general(state, val) {
            state.total_general = val
        },

        update_total_general_page(state, val) {
            state.total_general_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_provinces (state, v) { state.provinces = v },
        set_selected_province (state, v) { state.selected_province = v },

        set_cities (state, v) { state.cities = v },
        set_selected_city (state, v) { state.selected_city = v }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.page = context.state.current_page
                prm.search = context.state.search
                prm.city = context.state.selected_city ? context.state.selected_city.M_CityID : 0
                prm.province= context.state.selected_province ? context.state.selected_province.M_ProvinceID : 0
                prm.level = 0
                if (context.state.selected_level)
                    prm.level = context.state.selected_level.M_generalLevelID
                let resp = await api.search(prm)
                console.log(resp)
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

                    context.commit("update_generals", data.records)
                    context.commit("update_total_general", data.total)
                    context.commit("update_total_general_page", data.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async del(context, prm) {
            context.commit("update_search_status", 1)
            try {
                console.log(prm)
                prm.token = one_token()
                let resp = await api.del(prm)
                console.log(resp)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    
                    context.dispatch('search', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async search_province(context) {
            context.commit("set_common", ["search_province_status", 1])
            try {
                let prm = {token : one_token()}
                let resp = await api.search_province(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ['search_province_status', 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_province_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_provinces", resp.data.records)
                }
            } catch (e) {
                context.commit("set_common", ["search_province_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_city(context) {
            context.commit("set_common", ["search_city_status", 1])
            try {
                let prm = { token : one_token(), search:context.state.search_city, province_id:context.state.selected_province.M_ProvinceID }
                let resp = await api.search_city(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_city_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_city_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_cities", resp.data.records)
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}