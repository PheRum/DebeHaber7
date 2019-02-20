import VueI18n from 'vue-i18n';
import Vue from 'vue';

import enCommercial from './en/commercial.json';
import esCommercial from './es/commercial.json';

import enAccounting from './en/accounting.json';
import esAccounting from './es/accounting.json';

import enGeneral from './en/general.json';
import esGeneral from './es/general.json';

Vue.use(VueI18n);

const messages = {
    'en': {
        'general': enGeneral,
        'commercial': enCommercial,
        'accounting': enAccounting
    },
    'es': {
        'general': esGeneral,
        'commercial': esCommercial,
        'accounting': esAccounting
    }
};

const numberFormats = {
  'en': {
    currency: {
      style: 'currency', currency: 'USD'
    }
  },
  'es': {
    currency: {
      style: 'currency', currency: 'PYG', currencyDisplay: 'symbol'
    }
  }
}

const i18n = new VueI18n({
    locale: Spark.language, // set locale based on user settings.
    fallbackLocale: 'en', // set fallback locale
    messages, // set locale messages
    numberFormats,
});

export default i18n;
