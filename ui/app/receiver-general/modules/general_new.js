// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_general.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        selected_general: {},
        dialog_new: false,
        
        search_city_status: 0,
        search_city: '',
        cities: [],
        selected_city: null,

        search_village_status: 0,
        search_village: '',
        villages: [],
        selected_village: null,

        search_district_status: 0,
        search_district: '',
        districts: [],
        selected_district: null,

        search_province_status: 0,
        search_province: '',
        provinces: [],
        selected_province: null,

        search_general_level_status: 0,
        general_levels: [],
        selected_general_level: null,

        general_name: "",
        general_address: "",
        general_phone: "",
        general_note: "",
        general_email: "",

        user_general: "N",
        user_general_id: 0,
        user_general_username: "",
        user_general_password: "",
        user_general_password_confirm: "",
        change_password: false
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

        update_search_error_message(state, msg) { state.search_error_message = msg },

        update_search(state, search) { state.search = search },

        set_selected_general(state, val) { state.selected_general = val },

        update_search_status(state, val) { state.search_status = val },

        set_dialog_new (state, v) { state.dialog_new = v },

        set_cities (state, v) { state.cities = v },
        set_selected_city (state, v) { state.selected_city = v },

        set_villages (state, v) { state.villages = v },
        set_selected_village (state, v) { state.selected_village = v },

        set_districts (state, v) {  state.districts = v },
        set_selected_district (state, v) { state.selected_district = v },

        set_provinces (state, v) { state.provinces = v },
        set_selected_province (state, v) { state.selected_province = v },

        set_general_levels (state, v) { state.general_levels = v },
        set_selected_general_level (state, v) { state.selected_general_level = v }
    },
    actions: {
        async save(context) {
            context.commit('set_dialog_progress', true, {root:true})
            try {
                let prm = {token : one_token(), 
                            general_name: context.state.general_name,
                            general_address: context.state.general_address,
                            general_phone: context.state.general_phone,
                            general_note: context.state.general_note,
                            general_email: context.state.general_email,
                            general_level_id: context.state.selected_general_level.M_generalLevelID,
                            general_kelurahan_id: context.state.selected_village.M_KelurahanID,
                            user_general: context.state.user_general,
                            user_general_username: context.state.user_general_username,
                            user_general_password: context.state.user_general_password
                        }
                if (context.state.edit)
                    prm.general_id = context.rootState.general.selected_general.M_generalID

                let resp = await api.save(prm)
                console.log(resp)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_dialog_progress', false, {root:true})
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("general/search", {}, {root:true})
                    context.commit('set_dialog_progress', false, {root:true})
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
                context.commit('set_dialog_progress', false, {root:true})
                console.log(e)
            }
        },

        async search_province(context) {
            context.commit("set_common", ["search_province_status", 1])
            try {
                let prm = {token : one_token(), search:context.state.search_province}
                let resp = await api.search_province(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ['search_province_status', 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_province_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_provinces", resp.data.records)
                    if (context.state.edit) {
                        for (let pv of resp.data.records)
                            if (pv.M_ProvinceID == context.rootState.general.selected_general.M_ProvinceID) {
                                context.commit('set_selected_province', pv)
                                context.dispatch('search_city')
                            }
                    }
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
                    if (context.state.edit) {
                        for (let pv of resp.data.records)
                            if (pv.M_CityID == context.rootState.general.selected_general.M_CityID) {
                                context.commit('set_selected_city', pv)
                                context.dispatch('search_district')
                            }
                    }
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_district(context) {
            context.commit("set_common", ["search_district_status", 1])
            try {
                let prm = { token : one_token(), search:context.state.search_district, city_id:context.state.selected_city.M_CityID }
                let resp = await api.search_district(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_district_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_district_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_districts", resp.data.records)
                    if (context.state.edit) {
                        for (let pv of resp.data.records)
                            if (pv.M_DistrictID == context.rootState.general.selected_general.M_DistrictID) {
                                context.commit('set_selected_district', pv)
                                context.dispatch('search_village')
                            }
                    }
                }
            } catch (e) {
                context.commit("set_common", ["search_district_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_village(context) {
            context.commit("set_common", ["search_village_status", 1])
            try {
                let prm = { token : one_token(), search:context.state.search_village, district_id:context.state.selected_district.M_DistrictID }
                let resp = await api.search_village(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_village_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_village_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_villages", resp.data.records)
                    if (context.state.edit) {
                        for (let pv of resp.data.records)
                            if (pv.M_KelurahanID == context.rootState.general.selected_general.M_KelurahanID) {
                                context.commit('set_selected_village', pv)
                            }
                    }
                }
            } catch (e) {
                context.commit("set_common", ["search_village_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_general_level(context) {
            context.commit("set_common", ["search_general_level_status", 1])
            try {
                let prm = { token : one_token() }
                let resp = await api.search_general_level(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_general_level_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_general_level_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_general_levels", resp.data.records)
                }
            } catch (e) {
                context.commit("set_common", ["search_general_level_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async revoke_user(context) {
            try {
                let prm = {token : one_token(), 
                            general_id: context.rootState.general.selected_general.M_generalID
                            }

                let resp = await api.revoke_user(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("general/search", {}, {root:true})
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}