import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ReturnedProductsDetailComponent } from './returned-products-detail.component';

describe('ReturnedProductsDetailComponent', () => {
  let component: ReturnedProductsDetailComponent;
  let fixture: ComponentFixture<ReturnedProductsDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ReturnedProductsDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ReturnedProductsDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
