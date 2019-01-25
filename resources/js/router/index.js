
import FourZeroFour from '../views/404';

export default
[
  //This will cause 404 Errors to be redirected to proper site.
  {
    path: '/', component: FourZeroFour,
    meta: {
      requiresAuth: false
    }
  }
  //,
  // {
  //   path: '/:taxPayer/:cycle',
  //   component: DashBoard,
  //   name: 'index',
  //   meta: {
  //     requiresAuth: true,
  //
  //   },
  //   children:
  //   [
  //
  //   ]
  // }
]
