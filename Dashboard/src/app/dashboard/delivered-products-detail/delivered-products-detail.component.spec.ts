import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { DeliveredProductsDetailComponent } from './delivered-products-detail.component';

describe('DeliveredProductsDetailComponent', () => {
  let component: DeliveredProductsDetailComponent;
  let fixture: ComponentFixture<DeliveredProductsDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ DeliveredProductsDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(DeliveredProductsDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
