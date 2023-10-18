import {Component, EventEmitter, Input, OnDestroy, OnInit, Output} from '@angular/core';
import {faUsers} from "@fortawesome/free-solid-svg-icons/faUsers";
import {Observable, Subscription} from "rxjs";
import {FacilityService} from "../services/facility.service";

@Component({
  selector: 'cintas-customer-selection',
  templateUrl: './customer-selection.component.html',
  styleUrls: ['./customer-selection.component.scss']
})
export class CustomerSelectionComponent implements OnInit, OnDestroy {

  customerIcon = faUsers;

  @Input()
  selectedCustomer;

  @Input()
  classString = "";

  @Output()
  customerChange: EventEmitter<any> = new EventEmitter<any>();

  private customers$: Observable<any>;
  private customersSub: Subscription;
  customers: any[];

  constructor(private facilityService: FacilityService) {
  }

  ngOnInit() {
    this.customers$ = this.facilityService.getCustomers();
    this.customersSub = this.customers$.subscribe(res => {
      this.customers = res.data;
    });
  }

  ngOnDestroy(): void {
    if (this.customersSub) {
      this.customersSub.unsubscribe();
    }
  }

  onCustomerSelected(customer?) {
    this.selectedCustomer = customer ? customer : null;
    this.customerChange.emit(this.selectedCustomer);
  }

}
