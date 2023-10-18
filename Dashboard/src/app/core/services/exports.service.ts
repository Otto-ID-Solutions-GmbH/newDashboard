import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders, HttpResponse} from "@angular/common/http";
import {environment} from "../../../environments/environment";
import {saveAs} from 'file-saver';

@Injectable()
export class ExportsService {

  constructor(private http: HttpClient) {

  }

  public exportScanActionsList() {
    const headers = new HttpHeaders();
    headers.set('Accept', 'text/plain');
    return this.http.get(`${environment.apiUrl}/api/export/scan-actions`, {
      headers: headers,
      responseType: "arraybuffer",
      observe: 'response'
    });
  }

  public exportScanActionsDetailsList(start?, end?, type?, page?, sortBy?, sortDir?) {

    const p = {};

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

    const headers = new HttpHeaders();
    headers.set('Accept', 'text/plain');
    return this.http.get(`${environment.apiUrl}/api/export/scan-action-details`, {
      headers: headers,
      responseType: "arraybuffer",
      observe: 'response',
      params: p
    });
  }

  public exportAgeSummaryReport() {
    const headers = new HttpHeaders();
    headers.set('Accept', 'text/plain');
    return this.http.get(`${environment.apiUrl}/api/export/age-summary`, {
      headers: headers,
      responseType: "arraybuffer",
      observe: 'response'
    });
  }

  public exportItemsAtLocationData(locationType, locationCuid, period?) {

    const headers = new HttpHeaders();
    headers.set('Accept', 'text/plain');

    const p = {
      'period': period,
    };

    return this.http.get(`${environment.apiUrl}/api/export/no-items-per-product-type-over-time/${locationType}/${locationCuid}`, {
      headers: headers,
      responseType: 'arraybuffer',
      observe: 'response',
      params: p
    });
  }

  public exportIncomingOutgoingProductsOverTimeData(locationType, locationCuid, productTypeId, period?) {

    const headers = new HttpHeaders();
    headers.set('Accept', 'text/plain');

    const p = {
      'period': period,
    };

    return this.http.get(`${environment.apiUrl}/api/export/incoming-outgoing-products-over-time/${locationType}/${locationCuid}/${productTypeId}`, {
      headers: headers,
      responseType: 'arraybuffer',
      observe: 'response',
      params: p
    });
  }

  public saveToFileSystem(response: HttpResponse<any>) {
    const contentDispositionHeader: string = response.headers.get('Content-Disposition');
    const parts: string[] = contentDispositionHeader.split(';');
    const filename = parts[1].split('=')[1];
    const blob = new Blob([response.body], {type: 'text/plain'});
    saveAs(blob, filename);
  }

}
