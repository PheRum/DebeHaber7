
import FourZeroFour from '../views/404';
import DashBoard from '../views/index';

export default
[
  //This will cause 404 Errors to be redirected to proper site.
  {
    path: '/', component: FourZeroFour,
    meta: {
      requiresAuth: false
    }
  }
  ,
  {
    path: '/:taxPayer/:cycle/dashboard',
    component: DashBoard,
    name: 'dashboard',
    meta: {
      url: 'index',
      title: 'Dashboard',
      description: 'Some description',
      img: '/images/icons/type.svg',
      allow: {
        Accounting: false,
        Personal: false,
        Audit: true,
      }
    }
    //,
    // children:
    // [
    //   {
    //     path: '/:taxPayer/:cycle',
    //     component: DashBoard,
    //     name: 'index',
    //     meta: {
    //       requiresAuth: true,
    //       url: 'documents',
    //       title: 'Document',
    //       description: 'Some description',
    //       img: '/images/icons/type.svg',
    //       filters: ['name'],
    //       columns: [
    //         {field:'name',label:'Name', format:'string'},
    //         {field:'code_tempalte',label:'Template', format:'string'},
    //         {field:'mask',label:'Mask', format:'string'},
    //         {field:'updatedAt', label:'Updated On', format:'date'}
    //       ],
    //       actions: ['Edit', 'Delete'],
    //       form:'documentForm',
    //     }
    //   }
    // ]
  }
]
