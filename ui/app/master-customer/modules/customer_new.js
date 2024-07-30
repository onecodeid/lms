// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_customer.js"
import { URL, one_token, current_date } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        selected_customer: {},
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

        search_customer_level_status: 0,
        customer_levels: [],
        selected_customer_level: null,

        customer_name: "",
        customer_dob: null,
        customer_address: "",
        customer_phone: "",
        customer_phone2: "",
        customer_phone3: "",
        customer_note: "",
        customer_email: "",
        customer_postcode: "",
        customer_auto: "N",
        customer_mps: [],
        customer_join_date: current_date(),
        customer_due_payment: 'N',
        current_date: current_date(),

        user_customer: "N",
        user_customer_id: 0,
        user_customer_username: "",
        user_customer_password: "",
        user_customer_password_confirm: "",
        change_password: false,

        sexes: [{id:'M', text:'Laki - Laki'}, {id:'F', text:'Perempuan'}],
        selected_sex: null,

        mps: [],
        selected_mp: null,
        dialog_mp: false,
        mp_account_name: '',
        mp_account_url: ''
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

        set_object (state, v) {
            let name = v[0]
            let val = v[1]
            state[name] = val
        },

        update_search_error_message(state, msg) { state.search_error_message = msg },

        update_search(state, search) { state.search = search },

        set_selected_customer(state, val) { state.selected_customer = val },

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

        set_customer_levels (state, v) { state.customer_levels = v },
        set_selected_customer_level (state, v) { state.selected_customer_level = v },

        set_mps (state, v) { state.mps = v },
        set_selected_mp (state, v) { state.selected_mp = v },
        set_customer_mps (state, v) { state.customer_mps = v }
    },
    actions: {
        async save(context) {
            context.commit('set_dialog_progress', true, {root:true})
            try {
                let prm = {token : one_token(), 
                            customer_name: context.state.customer_name,
                            customer_sex: context.state.selected_sex?context.state.selected_sex.id:null,
                            customer_address: context.state.customer_address,
                            customer_phone: context.state.customer_phone,
                            customer_phone2: context.state.customer_phone2,
                            customer_phone3: context.state.customer_phone3,
                            customer_note: context.state.customer_note,
                            customer_email: context.state.customer_email,
                            customer_postcode: context.state.customer_postcode,
                            customer_auto: context.state.customer_auto,
                            customer_due_payment: context.state.customer_due_payment,
                            customer_level_id: context.state.selected_customer_level.M_CustomerLevelID,
                            customer_kelurahan_id: 0, // context.state.selected_village.M_KelurahanID,
                            customer_join_date: context.state.customer_join_date,
                            user_customer: context.state.user_customer,
                            user_customer_username: context.state.user_customer_username,
                            user_customer_password: context.state.user_customer_password,
                            customer_mps: JSON.stringify(context.state.customer_mps)
                        }
                        
                if (context.state.customer_dob)
                    prm.customer_dob = context.state.customer_dob

                if (context.state.edit)
                    prm.customer_id = context.rootState.customer.selected_customer.M_CustomerID

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
                    context.dispatch("customer/search", {}, {root:true})
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
                            if (pv.M_ProvinceID == context.rootState.customer.selected_customer.M_ProvinceID) {
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
                            if (pv.M_CityID == context.rootState.customer.selected_customer.M_CityID) {
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
                            if (pv.M_DistrictID == context.rootState.customer.selected_customer.M_DistrictID) {
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
                            if (pv.M_KelurahanID == context.rootState.customer.selected_customer.M_KelurahanID) {
                                context.commit('set_selected_village', pv)
                            }
                    }
                }
            } catch (e) {
                context.commit("set_common", ["search_village_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_customer_level(context) {
            context.commit("set_common", ["search_customer_level_status", 1])
            try {
                let prm = { token : one_token() }
                let resp = await api.search_customer_level(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_customer_level_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_customer_level_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_customer_levels", resp.data.records)
                }
            } catch (e) {
                context.commit("set_common", ["search_customer_level_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async revoke_user(context) {
            try {
                let prm = {token : one_token(), 
                            customer_id: context.rootState.customer.selected_customer.M_CustomerID
                            }

                let resp = await api.revoke_user(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("customer/search", {}, {root:true})
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async grant_user(context) {
            try {
                let prm = {token : one_token(), 
                            customer_id: context.rootState.customer.selected_customer.M_CustomerID
                            }

                let resp = await api.grant_user(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("customer/search", {}, {root:true})
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_mp(context) {
            context.commit("set_common", ["search_status", 1])
            try {
                let prm = { token : one_token() }
                let resp = await api.search_mp(prm)
                if (resp.status != "OK") {
                    context.commit("set_common", ["search_status", 3])
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_mps", resp.data.records)
                }
            } catch (e) {
                context.commit("set_common", ["search_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}