// This file can be replaced during build by using the `fileReplacements` array.
// `ng build --prod` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.

export const environment = {
  production: false,
  apiUrl: "http://127.0.0.1:8000",
  facilityCuid: "cjnadbv8q0003vwt9ee1e9sud",

  theme: {
    colorPalette: {
      domain: [
        '#022169', '#A5112C', '#006A9A', '#383838',
        '#720C1E', '#011136', '#B4495C', '#294482',
        '#513939', '#3A5FB5', '#848484', '#243606']
    },
    incomingOutgoingGoods: {
      domain: [
        '#243606', '#A5112C', '#022169'
      ]
    },
    baseView: [700, 400],
    timezone: 'EST'
  },

  kpiParameters: {
    itemLooseDays: 90
  }

};

/*
 * For easier debugging in development mode, you can import the following file
 * to ignore zone related error stack frames such as `zone.run`, `zoneDelegate.invokeTask`.
 *
 * This import should be commented out in production mode because it will have a negative impact
 * on performance if an error is thrown.
 */
// import 'zone.js/dist/zone-error';  // Included with Angular CLI.
