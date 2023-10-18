import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CustomerSummaryViewComponent } from './customer-summary-view.component';

describe('CustomerSummaryViewComponent', () => {
  let component: CustomerSummaryViewComponent;
  let fixture: ComponentFixture<CustomerSummaryViewComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CustomerSummaryViewComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CustomerSummaryViewComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
