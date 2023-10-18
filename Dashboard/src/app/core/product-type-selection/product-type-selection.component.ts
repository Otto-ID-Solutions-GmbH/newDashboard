import {Component, EventEmitter, Input, OnInit, Output} from '@angular/core';
import {ProductType} from '../models/product-type.model';
import {faObjectGroup} from '@fortawesome/free-solid-svg-icons/faObjectGroup';
import {HttpClient} from '@angular/common/http';
import {Observable} from 'rxjs';
import {environment} from '../../../environments/environment';
import {filter, map} from 'rxjs/operators';
import {faSpinner} from '@fortawesome/free-solid-svg-icons/faSpinner';

@Component({
  selector: 'cintas-product-type-selection',
  templateUrl: './product-type-selection.component.html',
  styleUrls: ['./product-type-selection.component.scss']
})
export class ProductTypeSelectionComponent implements OnInit {

  @Input()
  productTypeSelection: ProductType;

  @Output()
  productTypeSelectionChanged: EventEmitter<ProductType> = new EventEmitter<ProductType>();

  typeIcon = faObjectGroup;
  loadingIcon = faSpinner;

  types$: Observable<ProductType[]>;

  constructor(private http: HttpClient) {
  }

  ngOnInit() {
    this.types$ = this.http.get(environment.apiUrl + '/api/products/types', {
      headers: {
        Accept: 'application/json'
      }
    }).pipe(
      filter(res => res !== null),
      map((res: any) => {
        this.onTypeSelected(res.data[0]);
        return res.data;
      })
    );
  }

  onTypeSelected(p: ProductType) {
    this.productTypeSelection = p;
    this.productTypeSelectionChanged.emit(this.productTypeSelection);
  }

}
