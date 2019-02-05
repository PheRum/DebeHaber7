
import FourZeroFour from '../views/404';
import DashBoard from '../views/index';

import Commercial from '../views/commercials/index';
import SalesList from '../views/commercials/salesList';
import SalesForm from '../views/commercials/salesForm';
import PurchaseList from '../views/commercials/purchaseList';
import PurchaseForm from '../views/commercials/purchaseForm';
import CreditList from '../views/commercials/creditList';
import CreditForm from '../views/commercials/creditForm';
import DebitList from '../views/commercials/debitList';
import DebitForm from '../views/commercials/debitForm';

import Accounting from '../views/accounts/index';
import JournalForm from '../views/commercials/index';
import TemplateForm from '../views/commercials/index';
import ChartForm from '../views/commercials/index';

import Report from '../views/reports/index';

export default
[
    //This will cause 404 Errors to be redirected to proper site.
    // {
    //     path: '', component: FourZeroFour,
    // },
    {
        path: '/:taxPayer/:cycle/',
        component: DashBoard,
        name: 'taxPayer',
        meta: {
            url: 'index',
            title: 'Dashboard',
            description: 'Some description',
            img: '/img/apps/sales.svg',
        }
    },
    {
        path: '/:taxPayer/:cycle/commercial/',
        component: Commercial,
        name: 'commercialMenu',
        meta: {
            title: 'Dashboard',
            description: 'Some description',
            img: '/img/apps/sales.svg',
        },
        children:
        [
            {
                path: 'sales',
                component: SalesList,
                name: 'salesList',
                meta: {
                    apiUrl: 'sales',
                    title: 'Sales Book',
                    description: 'Some description',
                    img: '/img/apps/sales.svg',
                },
                children:
                [
                    {
                        path: '{id}',
                        component: SalesForm,
                        name: 'salesForm',
                        meta: {
                            title: 'Sales Invoice',
                        },

                    }
                ]
            },
            {
                path: 'credits',
                component: CreditList,
                name: 'creditList',
                meta: {
                    title: 'Credit Book',
                    description: 'Some description',
                    img: '/img/apps/credit-note.svg',
                },
                children:
                [
                    {
                        path: '{id}',
                        component: CreditForm,
                        name: 'creditForm',
                        meta: {
                            title: 'Credit Note',
                        },

                    }
                ]
            },
            {
                path: 'purchases',
                component: PurchaseList,
                name: 'purchaseList',
                meta: {
                    title: 'Purchase Book',
                    description: 'Some description',
                    img: '/img/apps/sales.svg',
                },
                children:
                [
                    {
                        path: '/{id}',
                        component: PurchaseForm,
                        name: 'purchaseForm',
                        meta: {
                            title: 'Purchase Invoice',
                            description: 'Some description',
                            img: '/img/apps/sales.svg',
                        },

                    }
                ]
            },
            {
                path: 'debits',
                component: DebitList,
                name: 'debitList',
                meta: {
                    title: 'Debit Book',
                    description: 'Some description',
                    img: '/img/apps/sales.svg',
                },
                children:
                [
                    {
                        path: '/{id}',
                        component: DebitForm,
                        name: 'debitForm',
                        meta: {
                            title: 'Debit Note',
                            description: 'Some description',
                            img: '/img/apps/sales.svg',
                        },

                    }
                ]
            }
        ]
    },
    {
        path: '/:taxPayer/:cycle/accounting/',
        component: Accounting,
        name: 'accountingMenu',
        meta: {
            title: 'Accounting',
            description: 'All your accounting data is here',
            img: '/img/apps/sales.svg',
        },
        children:
        [
            {
                path: 'journals',
                component: Commercial,
                name: 'journals',
                meta: {
                    title: 'Journal',
                    description: 'Some description',
                    img: '/img/apps/sales.svg',
                },

            }
        ]
    },
    {
        path: '/:taxPayer/:cycle/reports/',
        component: Report,
        name: 'reportingMenu',
        meta: {
            title: 'Reports',
            description: 'All your accounting data is here',
            img: '/img/apps/sales.svg',
        }
    }
]
