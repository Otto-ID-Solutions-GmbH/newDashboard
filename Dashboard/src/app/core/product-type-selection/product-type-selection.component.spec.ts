import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ProductTypeSelectionComponent } from './product-type-selection.component';

describe('ProductTypeSelectionComponent', () => {
  let component: ProductTypeSelectionComponent;
  let fixture: ComponentFixture<ProductTypeSelectionComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ProductTypeSelectionComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ProductTypeSelectionComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
