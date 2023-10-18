import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { IncomingOutgoingProductsDetailComponent } from './incoming-outgoing-products-detail.component';

describe('IncomingOutgoingProductsDetailComponent', () => {
  let component: IncomingOutgoingProductsDetailComponent;
  let fixture: ComponentFixture<IncomingOutgoingProductsDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ IncomingOutgoingProductsDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(IncomingOutgoingProductsDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
