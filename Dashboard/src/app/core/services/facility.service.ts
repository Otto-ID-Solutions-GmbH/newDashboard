import {Injectable} from '@angular/core';
import {environment} from "../../../environments/environment";
import {HttpClient} from "@angular/common/http";

@Injectable({
  providedIn: 'root'
})
export class FacilityService {

  constructor(private http: HttpClient) {
  }

  getCustomers() {
    return this.http.get(environment.apiUrl + '/api/facilities/cjnadbv8q0003vwt9ee1e9sud/customers');
  }
  
}
