import { URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',

        address: '',
        postcode: '',

        cities: [],
        selected_city: null,
        search_city: '',
        loading_city: false,

        search_village_status: 0,
        search_village: '',
        villages: [],
        selected_village: null,

        search_district_status: 0,
        search_district: '',
        districts: [],
        selected_district: null
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

        set_cities(state, v) {
            state.cities = v
        },

        set_selected_city(state, v) {
            state.selected_city = v
        },

        set_villages(state, v) { state.villages = v },
        set_selected_village(state, v) { state.selected_village = v },

        set_districts(state, v) { state.districts = v },
        set_selected_district(state, v) { state.selected_district = v }
    },

    actions: {
        async search_city(context) {
            context.commit('set_common', ['save', true])
            context.commit('set_common', ['loading_city', true])
            let prm = {
                token: one_token(),
                search: context.state.search_city
            }

            context.dispatch("system/postme", {
                url: "master/city/search_city_reverse",
                prm: prm,
                callback: function(d) {
                    context.commit("set_cities", d.records)
                    context.commit('set_common', ['loading_city', false])
                },
                failback: function(e) {
                    context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_district(context) {
            context.commit("set_common", ["search_district_status", 1])
            let prm = {
                token: one_token(),
                search: context.state.search_district,
                city_id: context.state.selected_city.city_id
            }

            context.dispatch("system/postme", {
                url: "master/district/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_common", ["search_district_status", 2])
                    context.commit("set_districts", d.records)
                },
                failback: function(e) {
                    context.commit("set_common", ["search_district_status", 3])
                }
            }, { root: true })
        },

        async search_village(context) {
            context.commit("set_common", ["search_village_status", 1])
            let prm = {
                token: one_token(),
                search: context.state.search_village,
                district_id: context.state.selected_district.M_DistrictID
            }

            context.dispatch("system/postme", {
                url: "master/kelurahan/search",
                prm: prm,
                callback: function(d) {
                    context.commit("set_common", ["search_village_status", 2])
                    context.commit("set_villages", d.records)
                },
                failback: function(e) {
                    context.commit("set_common", ["search_village_status", 3])
                }
            }, { root: true })
        },

        // async search_village(context) {
        //     context.commit("set_common", ["search_village_status", 1])
        //     try {
        //         let prm = { token : one_token(), search:context.state.search_village, district_id:context.state.selected_district.M_DistrictID }
        //         let resp = await api.search_village(prm)
        //         if (resp.status != "OK") {
        //             context.commit("set_common", ["search_village_status", 3])
        //             context.commit("update_search_error_message", resp.message)
        //         } else {
        //             context.commit("set_common", ["search_village_status", 2])
        //             context.commit("update_search_error_message", "")

        //             context.commit("set_villages", resp.data.records)
        //             if (context.state.edit) {
        //                 for (let pv of resp.data.records)
        //                     if (pv.M_KelurahanID == context.rootState.customer.selected_customer.M_KelurahanID) {
        //                         context.commit('set_selected_village', pv)
        //                     }
        //             }
        //         }
        //     } catch (e) {
        //         context.commit("set_common", ["search_village_status", 3])
        //         context.commit("update_search_error_message", e.message)
        //     }
        // }
    }


}