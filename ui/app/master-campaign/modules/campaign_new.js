// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_campaign.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        selected_campaign: {},
        dialog_new: false,

        campaign_name: "",

        expeditions:[],
        selected_expedition: null,
        services:[],
        selected_service:null,
        loading_service: false
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

        set_selected_campaign(state, val) {
            state.selected_campaign = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_dialog_new (state, v) {
            state.dialog_new = v
        },

        set_expeditions (state, v) {
            state.expeditions = v
        },

        set_selected_expedition (state, v) {
            state.selected_expedition = v
        },

        set_services (state, v) {
            state.services = v
        },

        set_selected_service (state, v) {
            state.selected_service = v
        }
    },
    actions: {
        async save(context) {
            
            try {
                let prm = {token : one_token(), 
                            campaign_name: context.state.campaign_name,
                            exp_id: context.state.selected_expedition.M_ExpeditionID,
                            service_name: context.state.selected_service.service
                        }
                if (context.state.edit)
                    prm.campaign_id = context.state.selected_campaign.campaign_id

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("set_common", ["search_status", 2])
                    context.commit("update_search_error_message", "")
                    
                    context.commit("set_common", ["dialog_new", false])
                    context.dispatch("campaign/search", {}, {root:true})
                }
            } catch (e) {
                context.commit("set_common", ["search_city_status", 3])
                context.commit("update_search_error_message", e.message)
            }
        },
        
        async search_expedition(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token: one_token() }

                let resp = await api.search_expedition(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_expeditions", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },
        
        async search_service(context) {
            context.commit("update_search_status", 1)
            context.commit('set_common', ['loading_service', true])
            try {
                let prm = {
                    token: one_token(),
                    to: 479,
                    courier: context.state.selected_expedition.M_ExpeditionROCode,
                    weight: 100
                }

                let resp = await api.search_service(prm)
                context.commit('set_common', ['loading_service', false])
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    
                    let data = JSON.parse(resp.data)
                    let c = data.rajaongkir.results[0].costs
                    context.commit("set_services", c)
                    if (context.state.edit) {
                        for (let i in c) {
                            console.log(c[i])
                            if (context.state.selected_campaign.service_name == c[i].service)
                                context.commit('set_selected_service', c[i])
                        }
                    }
                }
            } catch (e) {
                context.commit('set_common', ['loading_service', false])
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}