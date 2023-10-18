import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class TargetKpiService {

  constructor(private http: HttpClient) {
  }

  getTargetReachPerScan(period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'This month',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(
      environment.apiUrl + '/api/statistics/target-container-reach-per-scan', {params: p});
  }

  getContainerTargetReachToday() {
    return this.http.get(
      environment.apiUrl + '/api/statistics/target-container-reach-today');
  }

  getAggregatedBundleRatio(period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'Since 2 weeks',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(
      environment.apiUrl + '/api/statistics/aggregated-bundle-ratio-in-outscans', {params: p});
  }

  getBundleRatio(period?: string, start?: string, end?: string) {

    let p = {
      'period': period ? period : 'Since 2 weeks',
    };

    if (start) {
      p['start'] = start;
    }

    if (end) {
      p['end'] = end;
    }

    return this.http.get(
      environment.apiUrl + '/api/statistics/bundle-ratio-in-outscans', {params: p});
  }
}
