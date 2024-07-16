// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_payment.js"
import { URL, UPL_URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        UPL_URL: UPL_URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        
        orders: [],
        total_order: 0,
        selected_order: {},

        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],
        dialog_item: false,

        expeditions: [],
        total_expedition: 0,
        selected_expedition: {},

        services: [],
        total_service: 0,
        selected_service: {},

        current_page: 1,
        total_page: 1,
        sdate: new Date().toISOString().substr(0, 10),
        edate: new Date().toISOString().substr(0, 10),

        courier_cost: 0,
        weight_add: 0,
        weight_total: 0,

        accounts: [],
        selected_account: null,
        banks: [],
        selected_bank: null,
        transfer_date: '',
        transfer_time: '',
        transfer_amount: 0,
        transfer_note: '',
        transfer_name: '',
        receipt_img: '',

        statuses : [{value:'P',text:'Tagihan Terbayar'},{value:'C',text:'Pembayaran Dikonfirmasi'}],
        selected_status: {value:'P',text:'Tagihan Terbayar'}
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

        update_orders(state, data) {
            state.orders = data
        },

        set_selected_order(state, val) {
            state.selected_order = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_order(state, val) {
            state.total_order = val
        },

        update_items(state, data) {
            for (let i in data) {
                if (data[i].L_SoDetailApproved == 'N')
                    data[i].L_SoDetailSubTotal = (data[i].L_SoDetailPrice - data[i].L_SoDetailDiscTotal) * data[i].L_SoDetailApprovedQty
            }
            state.items = data

            let t = 0
            for (let d of data)
                t += parseFloat(d.M_ItemWeight * d.L_SoDetailApprovedQty);
                
            let so = state.selected_order
            so.L_SoSubTotalWeight = t
            
            state.selected_order = so
            state.weight_total = parseFloat(t) + parseFloat(state.weight_add)
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        set_expeditions(state, data) {
            state.expeditions = data
        },

        set_selected_expedition(state, val) {
            state.selected_expedition = val
        },

        set_total_expedition(state, v) {
            state.total_expedition = v
        },

        set_services(state, data) {
            state.services = data
        },

        set_selected_service(state, val) {
            state.selected_service = val
        },

        set_total_service(state, v) {
            state.total_service = v
        },

        set_selected_items(state, val) {
            state.selected_items = val
        },

        update_total_item(state, val) {
            state.total_item = val
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

        set_banks (state, v) {
            state.banks = v
        },

        set_selected_bank (state, v) {
            state.selected_bank = v
        },

        set_accounts (state, v) {
            state.accounts = v
        },

        set_selected_account (state, v) {
            state.selected_account = v
        },

        set_selected_status(state, val) {
            state.selected_status = val
        }
    },
    actions: {
        async search(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    search: context.state.query,
                    sdate: context.state.sdate,
                    edate: context.state.edate,
                    status: context.state.selected_status?context.state.selected_status.value:'A',
                    page: context.state.current_page,
                    token: one_token()
                }

                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_orders", resp.data.records)
                    context.commit("update_total_order", resp.data.total)
                    context.commit("set_common", ['total_page', resp.data.total_page])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_item(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    search: '',
                    order_id: context.state.selected_order.L_SoID,
                    token: one_token()
                }

                let resp = await api.search_item(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    console.log(resp)
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    let x = []
                    for (let i in data.records) {
                        let y = data.records[i]
                        if (y.L_SoDetailApproved == 'N')
                            y.L_SoDetailApprovedQty = parseFloat(y.L_SoDetailQty) > parseFloat(y.I_StockQty) ? y.I_StockQty : y.L_SoDetailQty
                        x.push(y.L_SoDetailID)
                        data.records[i] = y
                    }

                    context.commit("update_items", data.records)
                    context.commit("update_total_item", data.total)

                    context.commit('set_selected_items', x)

                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async confirm(context) {
            // MANUAL CONFIRMATION
            if (context.state.selected_order.F_PaymentID == null) {
                context.dispatch('save_payment')
                return
            }

            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token(),
                    payment_id: context.state.selected_order.F_PaymentID
                }

                let resp = await api.confirm(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('set_common', ['dialog_item', false])
                    context.dispatch('search', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_bank(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_bank(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_banks", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_account(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                let resp = await api.search_account(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_accounts", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async save_payment(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.transfer_date = context.state.transfer_date
                prm.transfer_time = context.state.transfer_time
                prm.transfer_name = context.state.transfer_name
                prm.transfer_note = context.state.transfer_note
                prm.account_id = context.state.selected_account.account_id
                prm.bank_id = context.state.selected_bank.M_BankID
                prm.order_id = context.state.selected_order.L_InvoiceID
                prm.payment_type_id = 1 // TRANSFER
                prm.amount = context.state.transfer_amount
                prm.receipt_img = context.state.receipt_img

                let resp = await api.save_payment(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    // context.commit("set_orders", resp.data.records)
                    if (context.state.id > 0)
                        window.location.replace('../sales-order-seller-list/')
                    else {
                        context.commit('set_common', ['snackbar', true])
                        context.commit('set_selected_order', null)
                        context.commit('set_selected_account', null)
                        context.commit('set_selected_bank', null)
                        context.commit('set_common', ['transfer_name', ''])
                        context.commit('set_common', ['transfer_date', new Date().toISOString().substr(0, 10)])
                        context.commit('set_common', ['transfer_time', new Date().toISOString().substr(11, 5)])
                        context.commit('set_common', ['transfer_amount', 0])

                        context.commit('set_common', ['dialog_item', false])
                        context.dispatch('search', {})
                    }    
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}