import { TestBed } from '@angular/core/testing';

import { ProductKpiService } from './product-kpi.service';

describe('ProductKpiService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: ProductKpiService = TestBed.get(ProductKpiService);
    expect(service).toBeTruthy();
  });
});
