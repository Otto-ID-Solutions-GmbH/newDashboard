import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ProductAgeSummaryDetailComponent } from './product-age-summary-detail.component';

describe('ProductAgeSummaryDetailComponent', () => {
  let component: ProductAgeSummaryDetailComponent;
  let fixture: ComponentFixture<ProductAgeSummaryDetailComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ProductAgeSummaryDetailComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ProductAgeSummaryDetailComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
