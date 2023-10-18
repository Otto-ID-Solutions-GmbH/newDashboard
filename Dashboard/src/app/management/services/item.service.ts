import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {environment} from "../../../environments/environment";

@Injectable({
  providedIn: 'root'
})
export class ItemService {

  constructor(private http: HttpClient) {

  }

  getItemStatuses(itemCuid) {
    return this.http.get(
      environment.apiUrl + '/api/items/details/' + itemCuid);
  }

}
