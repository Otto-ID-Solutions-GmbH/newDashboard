import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemsLostAtCustomerTableComponent } from './items-lost-at-customer-table.component';

describe('ItemsLostAtCustomerTableComponent', () => {
  let component: ItemsLostAtCustomerTableComponent;
  let fixture: ComponentFixture<ItemsLostAtCustomerTableComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemsLostAtCustomerTableComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemsLostAtCustomerTableComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
