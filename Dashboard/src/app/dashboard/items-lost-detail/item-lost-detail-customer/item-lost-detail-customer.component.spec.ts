import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemLostDetailCustomerComponent } from './item-lost-detail-customer.component';

describe('ItemLostDetailCustomerComponent', () => {
  let component: ItemLostDetailCustomerComponent;
  let fixture: ComponentFixture<ItemLostDetailCustomerComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemLostDetailCustomerComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemLostDetailCustomerComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
