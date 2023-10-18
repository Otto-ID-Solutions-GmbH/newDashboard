import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class ScanActionsService {

  constructor(private http: HttpClient) {

  }

  getScanActions(start?, end?, type?, page?, sortBy?, sortDir?) {

    let p = {};

    if (type) {
      p['type'] = type;
    }

    if (start) {
      p['start_date'] = start;
    }

    if (end) {
      p['end_date'] = end;
    }

    if (page) {
      p['page'] = page;
    }

    if (sortBy) {
      p['sort_by'] = sortBy;
    }

    if (sortDir) {
      p['sort_dir'] = sortDir;
    }

    return this.http.get(
      environment.apiUrl + '/api/reads/scan-actions', {params: p});

  }

}
