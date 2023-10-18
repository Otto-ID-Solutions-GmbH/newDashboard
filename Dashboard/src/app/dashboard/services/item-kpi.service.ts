import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";
import {from} from 'rxjs';

@Injectable()
export class ItemKpiService {

  constructor(private http: HttpClient) {
  }

  getItemsAtFacility(facilityCuid?) {

    if (facilityCuid) {
      return this.http.get(environment.apiUrl + '/api/statistics/items-at-facility/' + facilityCuid);
    } else {
      return this.http.get(environment.apiUrl + '/api/statistics/items-at-facility');
    }
  }

  getItemsAtLocations(customerId?: string, filterLocationType?: string, includeUnknown?) {

    let p = {};

    if (filterLocationType) {
      p['filterLocationType'] = filterLocationType
    }

    if (includeUnknown != null) {
      p['includeUnknown'] = includeUnknown
    }

    if (customerId) {
      return this.http.get(environment.apiUrl + '/api/statistics/items-at-location?customerId=' + customerId, {params: p});
    } else {
      return this.http.get(environment.apiUrl + '/api/statistics/items-at-location', {params: p});
    }
  }

  getNoOfLostItems(limitInDays = environment.kpiParameters.itemLooseDays, filterLocationType?: string, includeUnknown?: number) {
    let p = {};

    if (filterLocationType) {
      p['filterLocationType'] = filterLocationType
    }
    if (includeUnknown != null) {
      p['includeUnknown'] = includeUnknown
    }

    return this.http.get(environment.apiUrl + '/api/statistics/no-lost-items/' + limitInDays, {params: p});
  }

  getNoOfLostAndExistingItems(limitInDays = environment.kpiParameters.itemLooseDays, filterLocationType?: string) {
    let p = {};

    if (filterLocationType) {
      p['filterLocationType'] = filterLocationType
    }
    return this.http.get(environment.apiUrl + '/api/statistics/no-lost-and-existing-items/' + limitInDays, {params: p});
  }

  getNoLostItemsPerTime(limitInDays = environment.kpiParameters.itemLooseDays, period?: string, start?: string, end?: string, filterLocationType?: string) {

    let p = {
      'period': period ? period : 'This month',
    };

    if (filterLocationType) {
      p['filterLocationType'] = filterLocationType
    }

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(
      environment.apiUrl + '/api/statistics/no-lost-items/' + limitInDays, {params: p});
  }

  getNoItemsOverTime(locationType, locationCuid, period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'This year',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(
      environment.apiUrl + '/api/statistics/no-items-over-time/' + locationType + '/' + locationCuid, {params: p});
  }

  getIncomingOutgoingItemsOverTime(locationType, locationCuid, productTypeCuid, period?: string, start?: string, end?: string) {

    const p = {
      'period': period ? period : 'This year',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    if (locationType && locationCuid && productTypeCuid) {
      return this.http.get(
        environment.apiUrl + '/api/statistics/incoming-outgoing-products-over-time/' + locationType + '/' + locationCuid + '/' + productTypeCuid, {params: p});
    }

    return from([]);

  }

  getNoItemsPerCustomer(customerId?: string) {
    if (customerId) {
      return this.http.get(environment.apiUrl + '/api/statistics/items-per-location-per-product?customerId=' + customerId);
    } else {
      return this.http.get(environment.apiUrl + '/api/statistics/items-per-location-per-product');
    }

  }

  getNoOfLostItemsPerCustomer(filterLocationType?: string) {
    let p = {};

    if (filterLocationType) {
      p['filterLocationType'] = filterLocationType
    }
    return this.http.get(environment.apiUrl + '/api/statistics/no-lost-items-per-customer', {params: p});
  }

  getNoOfLostItemsForLocation(locationType: string, locationCuid: string) {
    return this.http.get(environment.apiUrl + '/api/statistics/no-lost-items-for-location/' + locationType + '/' + locationCuid);
  }

  getTopLocationsWithLostItems(n = 5, filterLocationType?: string) {
    let p = {};
    if (filterLocationType) {
      p['filterLocationType'] = filterLocationType
    }

    return this.http.get(environment.apiUrl + '/api/statistics/top-locations-with-lost-items/' + n, {params: p});
  }

  getLocationsWithLostItems(filterLocationType?: string) {
    let p = {};
    if (filterLocationType) {
      p['filterLocationType'] = filterLocationType
    }

    return this.http.get(environment.apiUrl + '/api/statistics/locations-with-lost-items', {params: p});
  }

}
