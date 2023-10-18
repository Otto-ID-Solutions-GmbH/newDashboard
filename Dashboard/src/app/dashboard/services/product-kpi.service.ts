import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class ProductKpiService {

  constructor(private http: HttpClient) {
  }

  getAvgRemaingLifetimePerProduct() {
    return this.http.get(environment.apiUrl + '/api/statistics/lifecycle-delta-per-product');
  }

  getAvgTurnaroundTimePerProductType(period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'Since 1 month',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(environment.apiUrl + '/api/statistics/avg-turnaround-time-per-product', {params: p});
  }

  getGroupedCycleCount() {
    return this.http.get(environment.apiUrl + '/api/statistics/grouped-avg-cycle-count');
  }

  getAvgCycleCountByProduct() {
    return this.http.get(environment.apiUrl + '/api/statistics/avg-cycle-count-by-product');
  }

  getDeliveredProductsInPeriod(period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'Since 2 weeks',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(environment.apiUrl + '/api/statistics/delivered-products-per-customer', {params: p});
  }

  getReturnedProductsInPeriod(period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'Since 2 weeks',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(environment.apiUrl + '/api/statistics/returned-products', {params: p});
  }

  getIncomingOutgoingProductsInPeriod(period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'Since 2 weeks',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(environment.apiUrl + '/api/statistics/incoming-outgoing-products', {params: p});
  }

}
