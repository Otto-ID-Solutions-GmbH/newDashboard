import { TestBed } from '@angular/core/testing';

import { ItemKpiService } from './item-kpi.service';

describe('ItemKpiService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: ItemKpiService = TestBed.get(ItemKpiService);
    expect(service).toBeTruthy();
  });
});
